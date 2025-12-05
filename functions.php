<?php
//$dsn0 = "mysql:host=localhost"; //old version
//$dsn0 = "mysql:host=mariadb;port=3306"; //docker version - uses service name instead // removed in favour of env values

$cacheFile = __DIR__ . '/data/cache/rows.json';
require_once __DIR__ . '/config/config.php';
require_once 'Auth.php';
//require_once 'index_testlogin.php'; #edited out to bypass login TODO:finish fixing later
//require_once '../config/vars.php'; #TODO: remove when finished phasing out in favour of .env and config.php

/*
 * Fetches GTFS data from VBB
 * returns modtime
 */
function getGTFS() {
    $url = 'https://www.vbb.de/fileadmin/user_upload/VBB/Dokumente/API-Datensaetze/gtfs-mastscharf/GTFS.zip';
    $modTime = 0;
    
    // Allow extra time for data processing
    set_time_limit(0); // infinite (seconds)

    // #TODO: variable return instead of echo - text would be better displayed after function call
    // create function reportOnData() BUT need modTime before fetch process. ALSO pass modTime by reference to allow updating?
    // access the modification date of first file in the zip if one already exists 
    // #TODO: read http headers instead? (keep this as backup?)
    if (file_exists('data/GTFS.zip')) {
        $zip = new ZipArchive();
        $zip->open('data/GTFS.zip');
        
        // get date of file
        $firstFileStats = $zip->statIndex(0);
        $modTime = $firstFileStats['mtime'];
        
        echo "<p>GTFS data already loaded, created " . date('Y-m-d H:i:s', $modTime) . "<br>"
        // . "Time now: " . date('Y-m-d H:i:s', time()) . "</p>"
        ;
        $zip->close();
    } else {
        echo "\nNo data has been loaded yet.\n";
    }
    
    // #TODO: split into functions reportOnData, archiveGTFS (new) and getGTFS, add update button (POST) and warning
    // comment out for fast testing until better handling finished

    //load new data if stale or missing
    if ($modTime < (time() - 200000)) {
        echo "<p>Looking for new data...";

        // store previous data with modTime in filename
        if (file_exists(__DIR__ . '/data/GTFS.zip')) {
            file_put_contents(__DIR__ . '/data/GTFS-'. date('Y-m-d', $modTime) . '.zip', fopen(__DIR__ . '/data/GTFS.zip', 'r'));
        }

        // get new data
        file_put_contents(__DIR__ . '/data/GTFS.zip', fopen($url, 'r'));

        //create new db
        echo " ...Done.<br> Creating new database, please wait...";
        createDB();

        //update modtime again
        $zip = new ZipArchive();
        if ($zip->open(__DIR__ . '/data/GTFS.zip') === TRUE) {
        $firstFileStats = $zip->statIndex(0);
        $modTime = $firstFileStats['mtime'];
        $zip->close();
    }

        echo "Most recent data successfully loaded, created " . date('Y-m-d H:i:s', $modTime) . "</p>";
    } else {
        echo "\nMost recent data already loaded, created " . date('Y-m-d H:i:s', $modTime);
    }
    return $modTime;
}

/* TODO: create db function here 
db (re)creation*/
function createDB() {
    //global $dsn0, $dsn, $username, $password; #TODO: delete when removed dsn0 and vars
    $conn = $conn2 = null;   //close any open PDO connections

    // Allow extra time for data processing
    set_time_limit(0); // infinite (seconds)

    // comment out for fast testing - 1.3GB!
    // open a connection    // note:w3s claims db name is required for connection, but it is not
    try {
    $conn = new PDO(DSN_NO_DB, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
    } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    }

    // create empty db // TODO:moved this to init file - clean up. also: find out whether to recreate whole DB / recreate just tables on new data / truncate tables and re-fill / keep adding new data to old with timestamps
//    try {
//        $sql = "CREATE DATABASE IF NOT EXISTS VBB_GTFS";
//        $conn->exec($sql);
//        echo "Database created.";
//    } catch(PDOException $e) {
//        echo "Error creating database: " . $e->getMessage();
//    }

    //connect to new test db
    $conn2 = new PDO(DSN_GTFS, DB_USER, DB_PASS,
        [
            PDO::MYSQL_ATTR_LOCAL_INFILE => true, // added to solve "local infile" data addition restriction #TODO: explore parameters
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    //workaround: drop all old tables TODO: find out why it is not working from main statements any more - it worked befor LOCAL_INFILE
    // $tables = ['agency', 'calendar', 'calendar_dates', 'routes', 'shapes', 'transfers', 'trips', 'stop_times', 'stops']; 
    // foreach ($tables as $table) {
    //     try {
    //         $conn2->exec("DROP TABLE IF EXISTS `$table`");
    //         echo "Dropped table $table\n";
    //     } catch (PDOException $e) {
    //         echo "Error dropping table $table: " . $e->getMessage() . "\n";
    //     }
    // }

    //get statements (split by ;) from script one at a time
    $gtfs_to_sql = file_get_contents(__DIR__ . '/migrations/sql/gtfs_to_mysql.sql');
    $statements =  explode(';', $gtfs_to_sql);
    # print_r($statements); //debug

    //unzip gtfs files #note: first install the PHP zip extension in Apache
    if (file_exists(__DIR__ . '/data/GTFS.zip')) {
            $zip = new ZipArchive();        // new ZipArchive object (allows use of zip methods)
            $zip->open(__DIR__ . '/data/GTFS.zip');
            @$zip->extractTo(__DIR__ . '/data/txt_latest');
    }

    //load data from unzipped files into db, using exec() to execute each statement
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            #echo $statement;
            try {
                $conn2->exec($statement);
            } catch (PDOException $e) {
                echo "\nError executing statement:\n$statement\n";
                echo $e->getMessage() . "\n\n";
            }
        }
    }

    $conn = $conn2 = null;   //close PDO connections again
}

/**
 * Fetches data from database or cache
 * @return array data rows
 */
function getEnds() {
    global $dsn, $username, $password, $cacheFile;    
    if (file_exists($cacheFile)) {
        echo "<br>INFO: Cache file exists, reading from cache";
        return json_decode(file_get_contents($cacheFile), true);
    } else {
        echo "<br>DEBUG: No cache file, connecting to database";
        try {
            $conn = new PDO($dsn, $username, $password, [
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            echo "<br>INFO: Database connection successful</p>";
            
            // create table for results in database itself
            $sqlFile = __DIR__ . '/migrations/sql/query_endstations.sql';
            if (!file_exists($sqlFile)) {
                echo "<br>ERROR: SQL file not found: $sqlFile";
                return [];
            }
            
            $sql_create_endstations = file_get_contents($sqlFile);
            
            $conn->exec('DROP TABLE IF EXISTS endstations');
            $conn->exec($sql_create_endstations);
            echo "<br>INFO: SQL executed successfully";
            
            // then only get the results each time
            $stmt = $conn->query('SELECT * FROM endstations');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<br>DEBUG: Query executed, found " . count($rows) . " rows";
            
            // also cache it so the database doesn't need to be used until refresh
            if (!$rows) {
                echo "<p>WARNING: No rows found, returning empty array</p>";
                return [];
            }
            
            file_put_contents($cacheFile, json_encode($rows));
            echo "<br>DEBUG: Cache file created";
            return $rows;
        } catch (PDOException $e) {
            echo "<p>ERROR: Database error: " . $e->getMessage() . "</p>";
            error_log("Connection failed: " . $e->getMessage());
            return [];
        }
    }
}
function getEnds0() {
    global $dsn, $username, $password, $cacheFile;
    if (file_exists($cacheFile)) {
        return json_decode(file_get_contents($cacheFile), true);
    } else {
        try {
            $conn = new PDO($dsn, $username, $password, [
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            // (re)create endstations table for results in the database itself
            $conn->exec('DROP TABLE IF EXISTS endstations');
            $sql_create_endstations = file_get_contents(__DIR__ . '/migrations/sql/query_endstations.sql');
            $conn->exec($sql_create_endstations);
            // then only get the results each time
            $stmt = $conn->query('SELECT * FROM endstations');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // also cache it so the database doesn't need to be used until refresh
            if (!$rows) {
                return [];
            }
            file_put_contents($cacheFile, json_encode($rows));
            return $rows;
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Generates HTML table for endstations and routes
 * 
 * @param array $rows Database result rows
 * @param array $superfluousStrings Strings to remove from headsigns
 * @return string HTML table of results
 */
function generateTableHtml($rows) {
    $tableHtml = '<table>
        <tr>
            <th>Route</th>
            <th></th>
            <th>End station</th>
            <th>Go!</th>
        </tr>';
    
    //generate columns 1-4, or 1-5 if user logged in
    if (count($rows) > 0) {
        foreach ($rows as $row) {
            // 1. Route names and colors
            $tableHtml .= "<tr>"
                . "<td bgcolor='" . htmlspecialchars($row['route_color']) 
                . "' style='text-align:center;color:#" . htmlspecialchars($row['route_text_color']) . ";'>" 
                . htmlspecialchars($row['route_short_name']) . "</td>"
                . "<td style='text-align:center;'>";
            
            // 2. Service logos
            if ($row['route_type'] == 109) {
                $tableHtml .= '<img src="assets/img/s-bahn-logo.png" alt="S-Bahn" style="height:22px;">';
            } elseif ($row['route_type'] == 400) {
                $tableHtml .= '<img src="assets/img/u-bahn-logo.png" alt="U-Bahn" style="height:22px;">';
            } else {
                $tableHtml .= htmlspecialchars($row['route_type']);
            }
            
            // 3. Destination text
            $tableHtml .= "</td>"
                . "<td class='destination-cell' style='color:" . ($row['route_type'] == 400 ? '#f5f5f5' : 'yellow') . ";'>"
                . htmlspecialchars(str_replace($superfluousStrings, "", $row['trip_headsign'])) 
                . "</td>";
            
            // 4. Google directions buttons
            $lat = htmlspecialchars($row['stop_lat']);
            $lon = htmlspecialchars($row['stop_lon']);
            $directionsUrl = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lon}&travelmode=transit";
            $tableHtml .= "><img height=16px src=/></button></td>";

            // 5. User input
            global $user_id;
            if(isset($user_id)) {
                $tableHtml .= "<td>
                <form>
                <input type='datetime-local' id='datetimeInput' value=>
                <input type='submit'>
                </form>
                </td>";
            }

            // 6. Close the row
            $tableHtml .= "</tr>";
        }
    } else {
        $tableHtml .= "<tr><td colspan='4'>0 results</td></tr>";
    }
    
    $tableHtml .= '</table>';
    return $tableHtml;
}
?>