<?php
//session_start(); //TODO: re-implement user login
require __DIR__ . '/../config/config.php';
//require_once '../functions.php';      //TODO: replace with database class? methods and delete
//require_once 'Auth.php';
//require_once 'index_testlogin.php'; #edited out to bypass login TODO:finish fixing later
//require_once '../config/vars.php'; #TODO: remove when finished phasing out in favour of .env and config.php
spl_autoload_register(function (string $class) {
    include '../classes/' . $class . '.php';
});
$user_id = 2; //for testing login features without login #TEST
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>EndStation Berlin</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="assets/css/main.css"/>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.vectorgrid/dist/Leaflet.VectorGrid.bundled.js"></script>
</head>
<body>
<a href="/"><h1>EndStation Berlin</h1></a>
<p class="subtitle">visit all the end-iest end stations of the S-Bahn and U-Bahn</p>

<div class="php-output"> <!--debug: show data retrieval info-->
    <?php
    //// Get data ////
    // 1. build the GTFS database (creation happens already in init) - needs CTE fix, skip for now
    //DatabaseGTFS::createDB();

    // 2. populateDB main database from GTFS database
    DatabaseMain::populateTableDefaults(Station::loadJSON());

    //2a. load straight from JSON into DB if GTFS db not working (as currently) #TODO
    $rows = Station::loadData();
    //DatabaseMain::loadDefaultEndtationsFromCache($rows);
    //DatabaseMain::populateTableDefaults();

    // 3. query database to fill object arrays?
    //$users = DatabaseMain::getUsers();
    //$endstations = DatabaseMain::getStations();


    //echo "<br>INFO: Query completed with " . count($rows) . " rows";



    ?>
</div>


<?php





// retrieve arrays of objects for each object type, using dummy object to call non-static methods

Station::loadData();        // loads from JSON
User::loadData();           // load hard-coded test values if static array is empty, otherwise from JSON
Visit::loadData();

// get input from user button clicks
$view = $_GET['view'] ?? 'showDashboard';
//echo $view . "<br>";
$stationName = $_GET['stationName'] ?? '';
//echo $stationName;
$station_id = $_GET['station_id'] ?? '';
$visit_id = $_GET['visit_id'] ?? '';

//// show the right page view ////
if ($view == 'addVisit') {
    $vNew = new Visit($station_id = null, $visit_id = null, $user_id = null, $guest_ids = []);
    $id = $vNew->getVisitId();
    Visit::writeVisitsJSON();
    header("Location: index.php?view=showDashboard.php");
} else if ($view === 'deleteVisit') {
    $id = $_GET['id'];
    Visit::deleteVisit($id);
    header("Location: index.php?action=showDashboard.php");
}

//load the correct view
include '../views/' . $view . '.php';
?>

</body>
</html>