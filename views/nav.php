<nav class="sidenav">
    <div class="user-info" style="padding: 15px; border-bottom: 1px solid #444; margin-bottom: 10px;">
        <?php if ($logged_in_user): ?>
            <!-- logged in user: show profile link and logout button -->
            <div style="color: #28a745; font-weight: bold;">
                welcome, <?= htmlspecialchars($logged_in_user->getUsername()) ?>
            </div>
            <div style="margin-top: 5px;">
                <a href="index.php?view=showUser&user_id=<?= $logged_in_user_id ?>" style="color: #aaa; font-size: 0.85rem; text-decoration: none;">view profile</a>
            </div>
            <form method="post" action="index.php" style="margin-top: 10px;">
                <button name="logout" class="btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">logout</button>
            </form>
        <?php else: ?>
            <!-- guest: show login button -->
            <div style="color: #ffc107; font-weight: bold; margin-bottom: 10px;">
                welcome, guest
            </div>
            <a href="index.php?view=login">
                <button style="padding: 5px 10px; font-size: 0.8rem;">log in</button>
            </a>
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