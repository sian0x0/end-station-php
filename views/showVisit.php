<?php
// $visit_id is provided by index.php

if (!$visit_id) {
    echo "<p>error: no visit id provided</p>";
    return;
}

// get the specific visit object
$visit = Visit::getVisitById((int)$visit_id);

if ($visit) {
    // retrieve the linked station object to get the name
    $station = Station::getStationById($visit->getStationId());
    $endstation_name = $station ? $station->getStationName() : "unknown station";

    echo "<h2>visit to " . htmlspecialchars($endstation_name) . " on " . date('j M Y', strtotime($visit->getVisitDatetime())) . "</h2>";
    echo "<p>user id: " . $visit->getUserId() . "</p>";

    $guests = $visit->getGuestIds();
    echo "<p>guests: " . (!empty($guests) ? implode(", ", $guests) : 'none') . "</p>";

    echo "<p>date and time: " . htmlspecialchars($visit->getVisitDatetime()) . "</p>";

    if ($visit->getNotes()) {
        echo "<p>notes: " . nl2br(htmlspecialchars($visit->getNotes())) . "</p>";
    }
} else {
    echo "<p>error: visit not found</p>";
}