<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// Test for authorized user
if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

$event = $_POST['currentEventNumber'];
$race = $_POST['currentRaceNumber'];

echo "Opening event $event race $race!";

$open_race_sql = "UPDATE `race` SET `window_closed` = 0 WHERE `race`.`event_id` = :event AND `race`.`race_number` = :race";
$open_race = $pdo->prepare($open_race_sql);
$open_race->execute(['event' => $event, 'race' => $race]);
header("Location: /races/?e={$event}&r={$race}&m=25&s=success");

?> 
