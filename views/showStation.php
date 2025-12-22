<?php
if (isset($_GET['station_id'])){
    $station_id = $_GET['station_id'];
    //$s = Station::getStationById($station_id);
} else {
    echo 'Error: no station ID given';
}

/// get station info from DB (not json for now)
$stationFromDB = DatabaseMain::getByID($station_id, 'endstations');
$endstation_name = $stationFromDB['trip_headsign'];
$endstation_route = $stationFromDB['route_short_name'];
$endstation_visits = Visit::getVisitsByStationId($station_id);

?>

<div class=container>
    <table class=station-profile>
        <tr>
            <td><img src="assets/img/stations/defaultstation.png" width="400"/></td> <!--source: Wikimedia Commons-->
            <td><h2><?= $endstation_name ?></h2>
                <p>line: <?= $endstation_route ?></p>
                <p>more info about the station</p>
                <p>more info about the station</p>
                <p>more info about the station</p>
                <p>more info about the station</p>
            </td>
        </tr>
    </table>
</div>

<div class=container>
    <div class=table-wrapper>
        <h3>Visits to this station</h3>
        <table class=visits-list>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>User</th>
                <th>Guests</th>
                <th></th>
            </tr>
            <?php
            foreach ($endstation_visits as $endstation_visit) {
                $visit_id = $endstation_visit['visit_id'];
                echo "<tr>";
                echo "<td>" . date('j M Y', strtotime($endstation_visit['visit_datetime'])) . "</td>";
                echo "<td>" . date('H:i', strtotime($endstation_visit['visit_datetime'])) . "</td>";
                echo "<td>" . User::getUserNameById($endstation_visit['user_id']) . "</td>";
                echo "<td>" . $endstation_visit['guest_ids'] . "</td>";
                echo "<td><a href='index.php?view=showVisit&visit_id=$visit_id'><button>View</button></a></td>";

                echo "</tr>";
            }
            ?>
                <?php
                //TODO..
                //            foreach ($s->getVisits() as $v) {
                //                echo "<tr><td>" . $v->getDate() . "</td><td>" . $v->getVisitors() . "</td></tr>";
                //            }
                ?>
            </tr>
        </table>
    </div>

    <div class='map'>
        <script> // generate and populate Leaflet map

            const map = L.map('map');
            map.setView({lat: 52.52, lng: 13.41}, 10);

            var Stadia_StamenToner = L.tileLayer('https://tiles.stadiamaps.com/tiles/stamen_toner/{z}/{x}/{y}{r}.{ext}', {
                minZoom: 0,
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://www.stamen.com/" target="_blank">Stamen Design</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                ext: 'png'
            }).addTo(map);

            // markers (GPT created - TODO: check params!)
            const stopData = <?= json_encode($rows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

            stopData.forEach(stop => {
                const lat = parseFloat(stop.stop_lat);
                const lon = parseFloat(stop.stop_lon);
                const color = stop.route_color ? `#${stop.route_color.replace(/^#?/, '')}` : '#3388ff';
                const routeName = stop.route_short_name;

                const latlngs = JSON.parse("[" + stop.line + "]");
                const line = new L.polyline(latlngs, {
                    color: color,
                    weight: 4,
                    opacity: 0.65,
                    smoothFactor: 2,
                });
                line.addTo(map);

                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
            background-color: ${color};
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            line-height: 28px;
            text-align: center;
            font-weight: bold;
            border: 0px solid #000;
            box-shadow: 0 0 2px #333;
            font-size: 12px;
            ">
            ${routeName}
        </div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                const marker = L.marker([lat, lon], {icon: icon}).addTo(map);
                marker.bindPopup(`<strong>${routeName}</strong><br>${stop.trip_headsign}`);
            });
            </div>
            </div>
            <button>Log a new visit</button>
            </div>