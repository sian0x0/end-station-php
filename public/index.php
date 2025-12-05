  <?php
  session_start();
  require __DIR__ . '/../config/config.php';
  require_once '../functions.php';      //TODO: replace with class methods and delete
  spl_autoload_register(function (string $class) {
    include 'classes/' . $class . '.php';

  ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>EndStation Berlin</title>

  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="assets/css/main.css" />

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.vectorgrid/dist/Leaflet.VectorGrid.bundled.js"></script>
</head>


<body>
  <h1>EndStation Berlin</h1>
  <p class="subtitle">visit all the end-iest end stations of the S-Bahn and U-Bahn</p>

<?php
  //// Get data ////
  // retrieve arrays of objects for each object type, using dummy object to call non-static methods
  (new User())->loadData();           // load hard-coded test values if static array is empty, otherwise from JSON
  (new Station())->loadData();
  (new Visit())->loadData();

  // get input from user button clicks
  $view = $_GET['view'] ?? 'showDashboard';
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
      header("Location: index.php?action=showListDepartments.php");
  } else { //default
  $view = 'listDepartments';
  }

  //load the correct view
  include 'views/' . $view . '.php';
</body>
</html>