<?php
//
////$action = $_GET["action"];
//$action = "addVisit";
//$visit_id = $_GET['visit_id'];
//
//$visit_date = $pdo->query("SELECT visit_date FROM visits WHERE visit_id = '$visit_id'")->fetch();
//$guests = $pdo->query("SELECT * FROM guests WHERE visit_id = '$visit_id'")->fetchAll();


//echo "<h2>$action</h2>";

?>
<form method="post" action="">
    <input type="hidden" name="action" value="$action">
    <input type="hidden" name="visit_id" value="$action">
    <input type='datetime-local' id='datetimeInput' value="$visit_date">
    <label for="endstation">Endstation:</label>
    <?php $endstations = Station::$stationsStaticArr; ?>
    <?php echo Station::makeSelectOption($endstations); ?>

    <input type="submit" value="Submit">
</form>

</form>
