<?php
spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';
});

//$id = $_GET['id'];
$id = 2; //draft test

//User::generateSampleUsers(); //now done in db

foreach (User::getUsersStaticArr() as $user) {
    if ($user->getId() == $id) {
        echo "<h2>User: " . $user->getUserName() . "</h2>";
        echo "Joined on " . $user->getJoinedDate() . "<br>";
        echo "<img src='" . $user->getProfilePicture() . "'><br>";
    }
}

?>
