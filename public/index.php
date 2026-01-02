<?php
//session_start(); // todo: re-implement user login
$logged_in_user_id = 2; // testing login bypass

require __DIR__ . '/../config/config.php';

spl_autoload_register(function (string $class) {
    include '../classes/' . $class . '.php';
});


$view = !empty($_GET['view']) ? $_GET['view'] : 'showDashboard'; //default

// populate database if it is empty
if (DatabaseMain::isDatabaseEmpty()) {
    DatabaseMain::populateTableDefaults(Station::loadJSON());
}

// load station objects for use in views
$stations = Station::getAll();

// retrieve user input
$station_id = $_GET['station_id'] ?? null;
$visit_id = $_GET['visit_id'] ?? null;

// process visit actions
if ($view === 'addVisit') {
    $visit = new Visit(
        endstation_id: (int)$station_id,
        user_id: $logged_in_user_id
    );
    $visit->save();
    header("Location: index.php?view=showDashboard");
    exit;
}

// process visit saving (add or edit)
if ($view === 'saveVisit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle multi-select array conversion to string
    $guest_ids_array = $_POST['guest_ids'] ?? [];
    $guest_ids_string = implode(',', $guest_ids_array);

    $visit = new Visit(
        user_id: (int)$_POST['user_id'],
        endstation_id: (int)$_POST['endstation_id'],
        visit_datetime: $_POST['visit_datetime'],
        guest_ids: $guest_ids_string,
        photo: $_POST['photo'],
        notes: $_POST['notes'],
        visit_id: !empty($_POST['visit_id']) ? (int)$_POST['visit_id'] : null
    );

    $visit->save();
    header("Location: index.php?view=showVisit&visit_id=" . $visit->getVisitId());
    exit;
}

if ($view === 'deleteVisit') {
    if ($visit_id) {
        Visit::deleteVisit((int)$visit_id);
    }
    header("Location: index.php?view=showDashboard");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>EndStation Berlin</title>
    <!-- leaflet css and js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="assets/css/main.css"/>
</head>
<body>
<?php include '../views/nav.php'; ?>
<div class="main">
    <a href="/"><h1>EndStation Berlin</h1></a>
    <?php
    // include the requested view file
    $viewPath = '../views/' . $view . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        include '../views/showDashboard.php';
    }
    ?>
</div>
</body>
</html>