<?php

spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';
});

$visit_id = $_GET['user_id'];


foreach (DatabaseMain::getAll('users') as $user) {
    if ($user['user_id'] == $user_id) {
        echo "<h2>User: " . $user['username'] . "</h2>";
        echo "Joined on " . date('j M Y', strtotime($user['join_date'])) . "<br>";
        echo "<img src='/assets/img/profile/" . $user['profile_picture'] . "'><br>";
    }
}