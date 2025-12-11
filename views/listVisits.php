<?php

spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';

});

$visitsFromDB = DatabaseMain::getAll('visits');

echo "<pre>";
echo "<h2>Visits</h2>";

echo "<table class='users-table'>";
echo "<tr>";
echo "<th>ID</th><th>Date</th><th>Station</th><th>Route</th><th>Visitor</th><th>Guests</th>";
echo "</tr>";

foreach ($visitsFromDB as $visit) {
    $visit_id = $visit['visit_id'];
    $visit_date = date('j M Y', strtotime($visit['visit_datetime']));
    $station_id = $visit['endstation_id'];

    $station_visited = DatabaseMain::getByID($station_id, 'endstations');
    $station_name = $station_visited['trip_headsign'];
    $route = $station_visited['route_short_name'];

    //var_dump($station_visited); //get line and name via id from here

    echo "<tr><td>" . $visit_id . "</td>";
    echo "<td><a href='index.php?view=showVisit&visit_id=$visit_id'>" . $visit_date . "</a>";
    echo "<td><a href='index.php?view=showStation&station_id=$station_id'>" . $station_name . "</a>";
    echo "<td>" . $route . "</td>";
    echo "<td><strong>" . $visit['user_id'] . "</strong></td>";
    echo "<td>" . "TODO: Station on date. [View more..]" . "</td>";
}
