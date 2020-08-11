<html>
<body>

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

// Get UID
$uid = $_SESSION['id'];

// URL needs to have the GET variables to work ex: http://localhost/races/?e=1&r=3
// Handle Event
$event = $_SESSION['current_event'];

// Handle Race
$race = $_POST['currentRace'];

if($_SESSION['site_memorial_race_enable'] == '1'){
    $memorial_race = '';
}


// Check to see if bet was placed correctly
if (!$_POST["horseSelection"]){
    header("Location: /races/?e={$event}&r={$race}&m=12&s=danger&f=" . $_POST["placeSelection"]);
    exit;
}
elseif (!$_POST["placeSelection"]) {
    header("Location: /races/?e={$event}&r={$race}&m=12&s=danger&p=" . $_POST["horseSelection"]);
    exit;
}

// SQL to check if the race window is still open
$race_info_sql = "SELECT * FROM `race` WHERE race.event_id = :event AND race.race_number = :race LIMIT 1";
$race_info_result = $pdo->prepare($race_info_sql);
$race_info_result->execute(['event' => $event, 'race' => $race]);
$race_info = $race_info_result->fetch();

if ($race_info['window_closed'] == '1') {
    echo "<script>alert('here');</script>";
    header("Location: /races/?e={$event}&r={$race}&m=26&s=danger");
    exit;
}

// SQL to determine this user's pick for this race
$pick_sql = "SELECT * FROM `pick` WHERE pick.user_id = :user_id AND pick.race_event_id = :event AND pick.race_race_number = :race LIMIT 1";
$pick_result = $pdo->prepare($pick_sql);
$pick_result->execute(['user_id' => $uid, 'event' => $event, 'race' => $race]);
$pick = $pick_result->fetch();

if ($pick) {
    // Check to see if the new bet is not the same bet as before
    if (($pick['horse_number'] == $_POST['horseSelection']) && ($pick['finish'] == $_POST['placeSelection'])) {
        // If it is, then redirect to original page
        header("Location: /races/?e={$event}&r={$race}");
        exit;
    }

    // Updating the pick (pick already exists)
    $update_horse_pick_sql = "UPDATE `pick` SET `horse_number` = :horse_number WHERE `pick`.`user_id` = :user_id AND `pick`.`race_event_id` = :event AND `pick`.`race_race_number` = :race";
    $update_horse_pick = $pdo->prepare($update_horse_pick_sql);
    $update_horse_pick->execute(['horse_number' => $_POST["horseSelection"], 'user_id' => $uid, 'event' => $event, 'race' => $race]);
    $update_place_pick_sql = "UPDATE `pick` SET `finish` = :place WHERE `pick`.`user_id` = :user_id AND `pick`.`race_event_id` = :event AND `pick`.`race_race_number` = :race";
    $update_place_pick = $pdo->prepare($update_place_pick_sql);
    $update_place_pick->execute(['place' => $_POST["placeSelection"], 'user_id' => $uid, 'event' => $event, 'race' => $race]);
    header("Location: /races/?e={$event}&r={$race}&m=14&s=success");
}
else {
    // Inserting a new pick (pick doesn't exist)
    $insert_horse_pick_sql = "INSERT INTO `pick` (user_id, race_event_id, race_race_number, horse_number, finish) VALUES (?, ?, ?, ?, ?)";
    $insert_horse_pick = $pdo->prepare($insert_horse_pick_sql);
    $insert_horse_pick->execute([$uid, $event, $race, $_POST["horseSelection"], $_POST["placeSelection"]]);
    header("Location: /races/?e={$event}&r={$race}&m=13&s=success");
}
?>