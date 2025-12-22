<?php
//
//$action = $_GET["action"];
$action = "addVisit";
$visit_id = $_GET['visit_id']??null;
//#todo: move these to db class
//$visit_datetime = $pdo->query("SELECT visit_date FROM visits WHERE visit_id = '$visit_id'")->fetch();
//$guests = $pdo->query("SELECT * FROM guests WHERE visit_id = '$visit_id'")->fetchAll();


//echo "<h2>$action</h2>";

?>
<div class='table-wrapper'>
    <h2>Add a new visit</h2>
<form method="post" action="">
    <input type="hidden" name="action" value="<?=$action?>">
    <input type="hidden" name="visit_id" value="<?=$visit_id?>">
    <input type='datetime-local' id='datetimeInput' value="<?=$visit_datetime?> ">
    <label for="endstation">Endstation:</label>
    <?php $endstations = Station::getAll(); ?>
    <?php echo Station::makeSelectOption($endstations); ?>
    <label for="endstation">Guests:</label>
    <?php $users = User::getAll(); ?>
    <?php echo User::makeSelectOption($users); ?>

    <input type="submit" value="Submit">
</form>
</div>
