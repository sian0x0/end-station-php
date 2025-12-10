<?php
spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';

});

//User::generateSampleUsers();
//print_r(User::getUsersStaticArr());

$usersFromDB = DatabaseMain::getAll('users');

echo "<pre>";
echo "<h2>Users</h2>";

echo "<table>";
echo "<tr>";
echo "<th>ID</th><th>Username</th><th>Joined date</th><th>Role</th><th>Visits</th></tr>";

foreach ($usersFromDB as $user) {
    echo "<tr>";
    echo "<td>" . $user['user_id'] . "</td>";
    echo "<td>" . $user['username'] . "</td>";
    echo "<td>" . $user['join_date'] . "</td>";
    echo "<td>" . $user['role'] . "</td>";
    echo "<td>" . "TODO" . "</td>";
}

//foreach (User::getUsersStaticArr() as $user) {
//    echo "Joined on $user->getJoinedDate()<br>";
//    echo "Profile picture: $user->getProfilePicture()<br>";
//}