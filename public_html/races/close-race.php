<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Test for authorized user
if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

$event = $_SESSION['current_event'];
$race = $_POST['currentRace'];

$close_race_sql = "UPDATE `race` SET `window_closed` = 1 WHERE `race`.`event_id` = :event AND `race`.`race_number` = :race";
$close_race = $pdo->prepare($close_race_sql);
$close_race->execute(['event' => $event, 'race' => $race]);
header("Location: /races/?e={$event}&r={$race}&m=22&s=success");

?> 
