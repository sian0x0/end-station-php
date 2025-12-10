<?php
$jsonFile = '../data/cache/rows.json';
$rows = json_decode(file_get_contents($jsonFile), true) ?? [];
//print_r($data);
#TODO: replace with function and always read from the json (speed)
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stations</title>
    <link rel="stylesheet" href="../public/assets/css/main.css">
</head>
<body>
    <h2>Stations</h2>
    <?php if (empty($rows)): ?>
        <p>No stations found.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Route</th>
                <th></th>
                <th>End station</th>
                <th>Go!</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $superfluousStrings = [" Bhf"," (Berlin)"," (TF)"];
            foreach ($rows as $row):
                ?>
                <tr>
                    <!-- 1. Route with color -->
                    <td bgcolor="<?= htmlspecialchars($row['route_color'] ?? '') ?>"
                        style="text-align:center;color:#<?= htmlspecialchars($row['route_text_color'] ?? '000000') ?>;">
                        <?= htmlspecialchars($row['route_short_name'] ?? '') ?>
                    </td>

                    <!-- 2. Service type logo -->
                    <td style="text-align:center;">
                        <?php if (($row['route_type'] ?? '') == 109): ?>
                            <img src="assets/img/s-bahn-logo.png" alt="S-Bahn" style="height:22px;">
                        <?php elseif (($row['route_type'] ?? '') == 400): ?>
                            <img src="assets/img/u-bahn-logo.png" alt="U-Bahn" style="height:22px;">
                        <?php else: ?>
                            <?= htmlspecialchars($row['route_type'] ?? '') ?>
                        <?php endif; ?>
                    </td>

                    <!-- 3. Destination -->
                    <td class="destination-cell"
                        style="color:<?= (($row['route_type'] ?? '') == 400 ? '#f5f5f5' : 'yellow') ?>;">
                        <?= htmlspecialchars(str_replace($superfluousStrings, "", $row['trip_headsign'] ?? '')) ?>
                    </td>

                    <!-- 4. Action buttons -->
                    <td>
                        <a href="../public/index.php?addEditVisit=<?= htmlspecialchars($row['parent_station'] ?? '') ?>"
                           class="btn btn-visit">Visit</a>
                        <a href="../public/index.php?showStation=<?= htmlspecialchars($row['parent_station'] ?? '') ?>"
                           class="btn btn-view">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>