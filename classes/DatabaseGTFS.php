<?php

class DatabaseGTFS
{
    public static function createDB() :void
    {
        //self::getGTFS();        // retrieve new files
        self::loadGTFS();       // put files in DB tables
        self::calculateEnds();  // run query to get endstations, store in new table in GTFS DB
    }

    /*
     * Fetches GTFS data from VBB
     * returns modtime
     */
    // #TODO: split into functions reportOnData, archiveGTFS (new) and getGTFS, add update button (POST) and warning
    public static function getGTFS()
    {
        $url = 'https://www.vbb.de/fileadmin/user_upload/VBB/Dokumente/API-Datensaetze/gtfs-mastscharf/GTFS.zip';
        $modTime = 0;

        if (file_exists('/../data/GTFS.zip')) {
            $zip = new ZipArchive();
            $zip->open('/../data/GTFS.zip');

            // get modtime of contained file
            $firstFileStats = $zip->statIndex(0);
            $modTime = $firstFileStats['mtime'];

            echo "<p>GTFS data already loaded, created " . date('Y-m-d H:i:s', $modTime) . "<br>"// . "Time now: " . date('Y-m-d H:i:s', time()) . "</p>"
            ;
            $zip->close();
        } else {
            echo "\nNo data has been loaded yet.\n";
        }
        // Allow extra time for data processing -- where was this? #TODO
        set_time_limit(0); // infinite (seconds)

        //loads new data only if stale (modtime earlier than set time ago) or missing
        if ($modTime < (time() - 200000)) {
            echo "<p>Looking for new data...";

            // store previous data with old modTime in filename
            if (file_exists(__DIR__ . '/../data/GTFS.zip')) {
                file_put_contents(__DIR__ . '/../data/GTFS-' . date('Y-m-d', $modTime) . '.zip', fopen(__DIR__ . '/../data/GTFS.zip', 'r'));
            }

            // get new data
            file_put_contents(__DIR__ . '/../data/GTFS.zip', fopen($url, 'r'));
            echo " ...Done.<br>";

            //get modtime of new files
            $zip = new ZipArchive();
            if ($zip->open(__DIR__ . '/../data/GTFS.zip') === TRUE) {
                $firstFileStats = $zip->statIndex(0);
                $modTime = $firstFileStats['mtime'];
                $zip->close();
            }

            //unzip gtfs files             #note: first install the PHP zip extension in Apache
            if (file_exists(__DIR__ . '/../data/GTFS.zip')) {
                $zip = new ZipArchive();        // new ZipArchive object (allows use of zip methods)
                $zip->open(__DIR__ . '/../data/GTFS.zip');
                @$zip->extractTo(__DIR__ . '/../data/txt_latest');
            }

            echo "Most recent data successfully loaded, created " . date('Y-m-d H:i:s', $modTime) . "</p>";
        } else {
            echo "\nMost recent data already loaded, created " . date('Y-m-d H:i:s', $modTime);
        }
        return $modTime;
    }

    // create empty db // TODO:moved this to init file - clean up. also: find out whether to recreate whole DB / recreate just tables on new data / truncate tables and re-fill / keep adding new data to old with timestamps
//    try {
//        $sql = "CREATE DATABASE IF NOT EXISTS VBB_GTFS";
//        $conn->exec($sql);
//        echo "DatabaseMain created.";
//    } catch(PDOException $e) {
//        echo "Error creating database: " . $e->getMessage();
//    }


    //load data from unzipped files into db
    public static function loadGTFS() :void {
        //get statements (split by ;) from script one at a time
        $gtfs_to_sql = file_get_contents(__DIR__ . '/../migrations/sql/gtfs_to_mysql.sql');
        $statements = explode(';', $gtfs_to_sql);
        # print_r($statements); //debug

        //load data from unzipped files into db, using exec() to execute each statement

        $conn = new PDO(DSN_GTFS, DB_USER, DB_PASS,  [
            PDO::MYSQL_ATTR_LOCAL_INFILE => true //needed additionally to other permissions to load infile specifically in PDO
        ]);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, );
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                #echo $statement;
                try {
                    $conn->exec($statement);
                } catch (PDOException $e) {
                    echo "\nError executing statement:\n$statement\n";
                    echo $e->getMessage() . "\n\n";
                }
            }
        }
    }

    public static function calculateEnds() {
        try {
            $conn = new PDO(DSN_MAIN, DB_USER, DB_PASS);
            $conn->exec("SET SESSION max_execution_time = 0");
            $conn->exec("SET SESSION group_concat_max_len = 10000000");

            echo "<br>INFO: database connection successful</p>";

            // create table for results in GTFS database itself
            $sqlFile = __DIR__ . '/../migrations/sql/query_endstations.sql';

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

            //TODO: make cache method separate
            file_put_contents(__DIR__ . '/../' . 'rows.json', json_encode($rows));
            echo "<br>DEBUG: Cache file created";
            return $rows;

        } catch (PDOException $e) {
            echo "<p>ERROR: DatabaseMain error: " . $e->getMessage() . "</p>";
            error_log("Connection failed: " . $e->getMessage());
            return [];
        }
    }

}