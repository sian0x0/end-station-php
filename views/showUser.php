<?php
spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';
});

$user_id = $_GET['user_id'];
$user = DatabaseMain::getbyID($user_id, 'users');
$visitsFromDB = DatabaseMain::getAll('visits');
$visitsByUser = [];
foreach ($visitsFromDB as $visit) {
    if ($visit['user_id'] == $user_id) {
        $visitsByUser[] = $visit;
    }
}

//user card
echo "<div class='container'>";
echo "<div class='table-wrapper'>";

echo "<h2>User: " . $user['username'] . "</h2>";
echo "Joined on " . date('j M Y', strtotime($user['join_date'])) . "<br>";
echo "<img src='/assets/img/profile/" . $user['profile_picture'] . "' width='200'><br>";

//echo "<a href='index.php?view=editUser&user_id=$user[user_id]'><button>Edit</button></a>"; //later: let users edit own profile
echo "</div>";


//user visits
echo "<div class='table-wrapper'>";

echo "<h2>". $user['username'] . "'s visits</h2>";
echo "<table>";
echo "<tr>";
echo "<th>Date</th><th>Station</th><th>Route</th><th>Guests</th>";
echo "</tr>";

foreach ($visitsByUser as $visit) {
    $visit_id = $visit['visit_id'];
    $visit_date = date('j M Y', strtotime($visit['visit_datetime']));
    $station_id = $visit['endstation_id'];
    $station_visited = DatabaseMain::getByID($station_id, 'endstations');
    $station_name = $station_visited['trip_headsign'];
    $route = $station_visited['route_short_name'];

    //var_dump($station_visited); //get line and name via id from here
    echo "<td><a href='index.php?view=showVisit&visit_id=$visit_id'>" . $visit_date . "</a>";
    echo "<td><a href='index.php?view=showStation&station_id=$station_id'>" . $station_name . "</a>";
    echo "<td>" . $route . "</td>";
    echo "<td>" . "TODO: list guests" . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";






//TODO: take directly from object array
//foreach (User::getUsersStaticArr() as $user) {
//    if ($user->getId() == $id) {
//        echo "<h2>User: " . $user->getUserName() . "</h2>";
//        echo "Joined on " . $user->getJoinedDate() . "<br>";
//        echo "<img src='" . $user->getProfilePicture() . "'><br>";
//    }
//}

?>
