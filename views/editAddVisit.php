<?php
// variables $visit_id, $logged_in_user_id, and $station_id are provided by index.php

if ($view === 'editVisit' && $visit_id) {
    // mode: edit existing visit
    $visit = Visit::getVisitById((int)$visit_id);
    $form_title = "edit visit #" . $visit->getVisitId();
} else {
    // mode: add new visit
    // use $station_id from get if available (e.g., from station profile)
    $visit = new Visit(
        endstation_id: (int)($station_id ?? 0),
        user_id: $logged_in_user_id,
        visit_datetime: date('Y-m-d H:i:s')
    );
    $form_title = "log new visit";
}

$stations = Station::getAll();
// format datetime for the html input field (yyyy-mm-ddthh:mm)
$html_datetime = date('Y-m-d\TH:i', strtotime($visit->getVisitDatetime()));
?>

<div class="container">
    <div class="table-wrapper" style="max-width: 600px; width: 100%;">
        <h2><?= htmlspecialchars($form_title) ?></h2>

    <form action="index.php?view=saveVisit" method="post" class="visit-form">
        <!-- hidden field to track if we are updating or inserting -->
        <input type="hidden" name="visit_id" value="<?= $visit->getVisitId() ?>">
        <input type="hidden" name="user_id" value="<?= $visit->getUserId() ?>">

        <div class="form-group">
            <label for="endstation_id">station:</label>
            <select name="endstation_id" id="endstation_id" required>
                <option value="">-- select station --</option>
                <?php foreach ($stations as $s): ?>
                    <option value="<?= $s->getStationId() ?>"
                        <?= ($s->getStationId() == $visit->getStationId()) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s->getStationName()) ?>
                        (<?= htmlspecialchars($s->getRouteShortName()) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="visit_datetime">date and time:</label>
            <input type="datetime-local" name="visit_datetime" id="visit_datetime"
                   value="<?= $html_datetime ?>" required>
        </div>

        <div class="form-group">
            <label for="guest_ids">guests:</label>
            <?= User::makeSelectOption(
                users: User::getAll(),
                selectedIds: $visit->getGuestIds(),
                excludeId: (int)$logged_in_user_id
            ) ?>
            <small>hold ctrl/cmd to select multiple</small>
        </div>

            <div class="form-group">
                <label for="photo">photo url:</label>
                <input type="text" name="photo" id="photo"
                       value="<?= htmlspecialchars($visit->getPhoto()) ?>">
            </div>

            <div class="form-group">
                <label for="notes">notes:</label>
                <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($visit->getNotes()) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit">save visit</button>
                <a href="index.php?view=showDashboard">
                    <button type="button" class="btn-secondary">cancel</button>
                </a>
            </div>
        </form>
    </div>
</div>