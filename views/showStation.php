<?php
$stationName = $_GET['stationName']; //TODO: change this to ID when ID implemented from parent station

foreach (Station::$stationsStaticArr as $s) {
    if ($s->getTripHeadsign() === $stationName) {
        return $s;
    }
    break; //this is needed to actually stop the pointer on the right object
}

?>

<div class=container>
    <table class=station-profile>
        <tr>
            <td><img src="assets/img/stations/defaultstation.png" width="400"/></td> <!--source: Wikimedia Commons-->
            <td><h2><?= $stationName ?></h2>
                <p>line: <?= $s->getRouteShortName() ?></p>
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
                <th>Visitors</th>
                <th></th>
            </tr>
            <tr>
                <td>01.10.25</td>
                <td><b>Lou</b>, Sian, Vullnet</td>
                <td>
                    <button>view</button>
                </td>
            <tr>
                <td>01.07.25</td>
                <td><b>Sian</b>, Vullnet</td>
                <td>
                    <button>view</button>
                </td>
            <tr>
                <td>01.05.25</td>
                <td><b>Lou</b>, Sian, Vullnet</td>
                <td>
                    <button>view</button>
                </td>
            <tr>
                <td>01.03.25</td>
                <td><b>Lou</b>,</td>
                <td>
                    <button>view</button>
                </td>
            <tr>
                <td>01.02.25</td>
                <td><b>Vullnet</b></td>
                <td>
                    <button>view</button>
                </td>
            <tr>
                <td>01.01.25</td>
                <td><b>Lou</b>, Sian, Vullnet</td>
                <td>
                    <button>view</button>
                </td>
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