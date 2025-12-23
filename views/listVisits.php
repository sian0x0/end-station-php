<?php
// load all visits as objects
$visits = Visit::getAll();
?>

<div class="container">
    <h2>Visits</h2>
    <div class="table-wrapper">
        <?= Visit::generateTableHtml($visits) ?>
    </div>
</div>