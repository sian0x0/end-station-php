<?php
spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';

});

//User::generateSampleUsers();
//print_r(User::getUsersStaticArr());

$usersFromDB = DatabaseMain::getAll('users');

echo "<pre>";
echo "<h2>Users</h2>";

echo "<table class='users-table'>";
echo "<tr>";
echo "<th>ID</th><th>Username</th><th>Joined date</th><th>Role</th><th>Last visit</th></tr>";

foreach ($usersFromDB as $user) {
    $user_id = $user['user_id'];
    echo "<tr>";
    echo "<td>" . $user_id . "</td>";
    echo "<td><strong>" . "<a href='index.php?view=showUser&user_id=$user_id'>". $user['username'] . "</a></strong></td>";
    echo "<td>" . date('j M Y', strtotime($user['join_date'])) . "</td>";
    echo "<td>" . $user['role'] . "</td>";
    echo "<td>" . "TODO: Station on date. [View more..]" . "</td>";
}
