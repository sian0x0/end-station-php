<?php
// $station_id is provided by index.php
$station = Station::getStationById((int)$station_id);

if (!$station) {
    echo "<p>error: station not found.</p>";
    return;
}

$station_visits = Visit::getVisitsByStationId($station->getStationId());
?>

<!-- top section: station profile -->
<div class="container">
    <table class="station-profile">
        <tr>
            <td><img src="assets/img/stations/defaultstation.png" width="400" alt="station photo"/></td>
            <td>
                <header style="display: flex; align-items: center; gap: 10px;">
                    <span style="background-color: #<?= ltrim($station->getRouteColor(), '#') ?>; color: #<?= ltrim($station->getRouteTextColor(), '#') ?>; padding: 2px 8px; border-radius: 4px; font-weight: bold;">
                        <?= htmlspecialchars($station->getRouteShortName()) ?>
                    </span>
                    <h2 style="margin: 0;"><?= htmlspecialchars($station->getStationName()) ?></h2>
                </header>
                <p>line: <?= htmlspecialchars($station->getRouteShortName()) ?></p>
                <a href="index.php?view=editAddVisit&station_id=<?= $station->getStationId() ?>">
                    <button style="margin-top: 10px; cursor: pointer;">log a new visit</button>
                </a>
            </td>
        </tr>
    </table>
</div>

<!-- middle section: visits on left, map on right -->
<div class="container" style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">

    <!-- left side: visits list -->
    <div class="table-wrapper" style="flex: 1; min-width: 350px;">
        <h3>visits to this station</h3>
        <table class="visits-list">
            <thead>
            <tr>
                <th>date</th>
                <th>user</th>
                <th>guests</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($station_visits)): ?>
                <tr><td colspan="4">no visits recorded.</td></tr>
            <?php else: ?>
                <?php foreach ($station_visits as $v): ?>
                    <?php $visitor = User::getUserById($v->getUserId()); ?>
                    <tr>
                        <td><?= date('j M Y', strtotime($v->getVisitDatetime())) ?></td>
                        <td>
                            <a href="index.php?view=showUser&user_id=<?= $v->getUserId() ?>">
                                <?= htmlspecialchars($visitor ? $visitor->getUsername() : "user #" . $v->getUserId()) ?>
                            </a>
                        </td>
                        <td><?= count($v->getGuestIds()) ?></td>
                        <td><a href="index.php?view=showVisit&visit_id=<?= $v->getVisitId() ?>"><button>view</button></a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- right side: map and description -->
    <div class="map-and-description" style="flex: 1; min-width: 350px;">
        <div id="station-map" style="height: 450px; width: 100%; border-radius: 8px; border: 1px solid #ccc;"></div>
        <div id="text-container" style="margin-top: 15px;">
            <p><strong>location:</strong> <?= htmlspecialchars($station->getStationName()) ?></p>
            <p>this end station serves the <?= htmlspecialchars($station->getRouteShortName()) ?> line.</p>
        </div>
    </div>
</div>

<script> // leaflet map initialization
    document.addEventListener('DOMContentLoaded', function() {
        const lat = parseFloat("<?= $station->getStopLat() ?>");
        const lon = parseFloat("<?= $station->getStopLon() ?>");
        const routeColor = "#" + "<?= ltrim($station->getRouteColor(), '#') ?>";

        if (isNaN(lat) || isNaN(lon)) return;

        const map = L.map('station-map').setView([lat, lon], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // render station line geometry
        const lineStr = <?= json_encode($station->getLine()) ?>;
        if (lineStr && lineStr.length > 0) {
            try {
                // wrap coordinate pairs in brackets if required by your data format
                const latlngs = JSON.parse("[" + lineStr + "]");
                L.polyline(latlngs, {
                    color: routeColor,
                    weight: 6,
                    opacity: 0.8
                }).addTo(map);
            } catch (e) {
                console.warn("failed to parse line geometry for station");
            }
        }

        // define marker icon
        const icon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: ${routeColor}; color: white; border-radius: 50%; width: 32px; height: 32px; line-height: 32px; text-align: center; font-weight: bold; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);">${<?= json_encode($station->getRouteShortName()) ?>}</div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        // add marker and open popup
        L.marker([lat, lon], {icon: icon})
            .addTo(map)
            .bindPopup("<strong>" + <?= json_encode($station->getStationName()) ?> + "</strong>")
            .openPopup();

        // recalculate map size after flex layout renders
        setTimeout(() => { map.invalidateSize(); }, 200);
    });
</script>