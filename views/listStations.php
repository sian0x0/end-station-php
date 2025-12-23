<?php
$stations = Station::getAll();

if (empty($stations)) {
    echo "<p>No stations found</p>";
} else {
    echo Station::generateStationTableHtml();
}

