<nav class="sidenav">
    <div class="user-info" style="padding: 15px; border-bottom: 1px solid #444; margin-bottom: 10px; font-size: 0.9em;">
        <?php
        // get current logged in user object
        $currentUser = $logged_in_user_id ? User::getUserById((int)$logged_in_user_id) : null;

        if ($currentUser): ?>
            <div style="color: #28a745; font-weight: bold;">
                welcome, <?= htmlspecialchars($currentUser->getUsername()) ?>
            </div>
            <a href="index.php?view=showUser&user_id=<?= $currentUser->getUserId() ?>" style="font-size: 0.8em; color: #aaa;">view profile</a>
        <?php else: ?>
            <div style="color: #ffc107;">
                guest: <a href="index.php?view=login" style="color: #ffc107; text-decoration: underline;">log in</a>
            </div>
        <?php endif; ?>
    </div>

    <ul>
        <li><a href="index.php?view=editAddVisit">add visit</a></li>
        <li>view:
            <ul>
                <li><a href="index.php?view=showDashboard">dashboard</a></li>
                <li><a href="index.php?view=listVisits">visits</a></li>
                <li><a href="index.php?view=listUsers">users</a></li>
            </ul>
        </li>
    </ul>
</nav>