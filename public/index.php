<?php
session_start();
require_once '../config/vars.php';      // define/include variables before functions
require_once '../functions.php';
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

  <div class="php-output">
    <?php getGTFS(); ?>
    <?php $rows = getEnds(); ?>
    <?php echo "<br>INFO: Query completed with " . count($rows) . " rows"; ?>
  </div>

  <div class="container">

    <div class="table-wrapper" tabindex="0" aria-label="Table of End Stations">
      <?php echo generateTableHtml($rows); ?>
    </div>

    <div class="map-and-description" style="flex: 1; max-width: 700px;">
      <div id="map"></div>
      <div id="text-container">
        <p>A truly satisfying end station is one that:</p>
          <ul>
              <li>is at <strong>the end of its own line</strong>;</li>
              <li><strong>lacks any interchange</strong> to other S-Bahn or U-Bahn lines.</li>
          </ul>
      </div>
    </div>

  </div>

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

    const marker = L.marker([lat, lon], { icon: icon }).addTo(map);
    marker.bindPopup(`<strong>${routeName}</strong><br>${stop.trip_headsign}`);
});
</script>

</body>
</html>