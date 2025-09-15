<?php
session_start();

$url = 'https://www.vbb.de/fileadmin/user_upload/VBB/Dokumente/API-Datensaetze/gtfs-mastscharf/GTFS.zip';
$modTime = '0';

// access modification date of first file in the zip if one already exists
if (file_exists('data/GTFS.zip')) {
    $zip = new ZipArchive();        // new object, instance of class ZipArchive (allows use of zip methods) - must first be enabled in in php.ini (so far i just uncommented extension=zip)
    $zip->open('data/GTFS.zip');    // note: -> (object operator) accesses object properties #TODO: read about how

    // get date                             // TODO: make into mini function
    $firstFileStats = $zip->statIndex(0);   // returns an ass. array of file stats for file at index 0
    global $modTime; 
    $modTime = $firstFileStats['mtime'];    // note: as Unix timestamp in seconds

    echo "Some GTFS data is already loaded, created at: " . date('Y-m-d H:i:s', $modTime);
    echo "\nTime now: " . date('Y-m-d H:i:s', time()); // TODO: maybe add button to get new
} else {
    echo "\nNo data has been loaded yet.\n";
}

// load new data if stale or missing    //TODO: don't replace file if it's the same one (store in temp first?)
if ($modTime < (time() - 200000)) {     // Fudge for fetch limitation. 200000 sec is about 2.3 days (VBB updates data each wed and fri)
    echo "\nLooking for new data...";
    // store previous data with modTime in filename
    file_put_contents('data/GTFS-'. date('Y-m-d', $modTime) . '.zip', fopen('data/GTFS.zip', 'r'));
    // get new data
    file_put_contents('data/GTFS.zip', fopen($url, 'r'));
    echo "\n...Done\nMost recent data loaded, created at " . date('Y-m-d H:i:s', $modTime);
} else {
    echo "\nMost recent data already loaded, created at " . date('Y-m-d H:i:s', $modTime);
}

// load gtfs to a mysql db (see testdb.php)

// query endstations (see testquery.php)

// display endstations (also under testquery.php)

?>  
