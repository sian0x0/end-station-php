<?php
spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';

});

User::generateSampleUsers();

print_r(User::getUsersStaticArr());

foreach (User::$usersStaticArr as $user) {
    echo "Joined on $user->getJoinedDate()<br>";
    echo "Profile picture: $user->getProfilePicture()<br>";
}