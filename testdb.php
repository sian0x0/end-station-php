<?php
$conn = $conn2 = null;   //close PDO connections
$dsn = "mysql:host=localhost";
$username = "user101";
$password = "Bahn.U7.U8.";

// db (re)creation commented out for now - 1.3GB!

// open a connection    // note:w3s claims db name is required for connection, but it is not
try {
  $conn = new PDO($dsn, $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

// create empty db // TODO:find out whether to recreate whole DB / recreate just tables on new data / truncate tables and re-fill / keep adding new data to old with timestamps
try {
    // SQL to create a database
    $sql = "CREATE DATABASE IF NOT EXISTS test_database";
    $conn->exec($sql);      // TODO: read about exec() and when to use
    echo "DatabaseMain created.";
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
}

//connect to new test db
$dsn ="mysql:host=localhost;dbname=test_database_s_u";
$conn2 = new PDO($dsn, $username, $password,
    [
        PDO::MYSQL_ATTR_LOCAL_INFILE => true, // trying this to solve local infile restriction #TODO: explore parameters
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
$gtfs_to_sql = file_get_contents('gtfs_to_mysql.sql');
$statements =  explode(';', $gtfs_to_sql);
# print_r($statements); //debug

//unzip gtfs files
if (file_exists('data/GTFS.zip')) {
        $zip = new ZipArchive();        // new ZipArchive object (allows use of zip methods)
        $zip->open('data/GTFS.zip');
        $zip->extractTo(".");
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

$conn = $conn2 = null;   //close PDO connections

?>
