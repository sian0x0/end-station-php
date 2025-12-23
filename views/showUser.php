<?php
$target_user_id = $_GET['user_id'] ?? null;

if (!$target_user_id) {
    echo "<p>error: user id not provided</p>";
    return;
}

$target_user = User::getUserById((int)$target_user_id);

if (!$target_user) {
    echo "<p>error: user not found.</p>";
    return;
}

// get user's visits
$visitsByUser = Visit::getVisitsByUserId($target_user->getUserId());

// #TODO: combine with Visit::generate...
?>

<div class="container">
    <!-- user profile card -->
    <div class="table-wrapper">
        <h2>user: <?= htmlspecialchars($target_user->getUsername()) ?></h2>
        <p>joined on <?= date('j M Y', strtotime($target_user->getJoindate())) ?></p>
        <img src="/assets/img/profile/<?= htmlspecialchars($target_user->getProfilePicture()) ?>"
             alt="profile picture"
             style="width: 200px; border-radius: 4px;">
    </div>

    <!-- user visits history -->
    <div class="table-wrapper">
        <h3><?= htmlspecialchars($target_user->getUsername()) ?>'s visits</h3>
        <table>
            <thead>
            <tr>
                <th>date</th>
                <th>station</th>
                <th>route</th>
                <th>guests</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($visitsByUser)): ?>
                <tr><td colspan="4">no visits recorded yet</td></tr>
            <?php else: ?>
                <?php foreach ($visitsByUser as $visit): ?>
                    <?php
                    $station = Station::getStationById($visit->getStationId());
                    ?>
                    <tr>
                        <td>
                            <a href="index.php?view=showVisit&visit_id=<?= $visit->getVisitId() ?>">
                                <?= date('j M Y', strtotime($visit->getVisitDatetime())) ?>
                            </a>
                        </td>
                        <td>
                            <a href="index.php?view=showStation&station_id=<?= $visit->getStationId() ?>">
                                <?= htmlspecialchars($station ? $station->getStationName() : "unknown") ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($station ? $station->getRouteShortName() : "n/a") ?></td>
                        <td>
                            <?php
                            $guests = $visit->getGuestIds();
                            echo !empty($guests) ? htmlspecialchars(implode(", ", $guests)) : "none";
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>