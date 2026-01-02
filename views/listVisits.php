<?php
// load all visits as objects
$visits = Visit::getAll();
?>

<div class="container">
    <div class="text-container">
        <h2>All EndStation visits</h2>
        <?= Visit::generateTableHtml($visits) ?>
    </div>
</div>