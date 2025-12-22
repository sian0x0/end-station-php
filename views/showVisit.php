<?php

spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';
});

$visit_id = $_GET['visit_id'];

foreach (DatabaseMain::getAll('visits') as $visit) {
    if ($visit['visit_id'] == $visit_id) {
        //print_r($visit);
        $endstation_name = Station::getStationById($visit['endstation_id'])->getStationName();
        echo "<h2>Visit to " . $endstation_name . " on " . date('j M Y', strtotime($visit['visit_datetime'])) . "</h2>";
        echo "<p>User ID: " . $visit['user_id'] . "</p>";
        echo "<p>Guests: " . ($visit['guest_ids']?? 'none') . "</p>";
        echo "<p>Date and time: " . $visit['visit_datetime'] . "</p>";
    }
}