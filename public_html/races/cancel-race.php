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

$close_race_sql = "UPDATE `race` SET `cancelled` = 1 WHERE `race`.`event_id` = :event AND `race`.`race_number` = :race";
$close_race = $pdo->prepare($close_race_sql);
$close_race->execute(['event' => $event, 'race' => $race]);
header("Location: /races/?e={$event}&r={$race}&m=23&s=success");

?> 
