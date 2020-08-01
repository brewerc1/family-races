<?php
// Refactoring in Progress

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
//ob_start('template');
session_start();

// set the page title for the template
//$page_title = "Manage an Event";
//$javascript = "";

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}


if (isset($_POST["update_event"])) {

    $db = json_decode($_POST["db"]);

//    if (!empty($db->{'pot'})) {
//        $pot = $db->{'pot'};
//
//        // Update POT
//        $sql = "UPDATE event SET pot=:pot WHERE id=:id";
//        $stmt= $pdo->prepare($sql);
//        $stmt->execute(['pot' => $pot, 'id' => $event_id]);
//    }
        // create new race
    $races = $db->{'r'};
    $sql = "INSERT INTO race (event_id, race_number) VALUES (:event_id, :race_number)";
    $stmt = $pdo->prepare($sql);
    for ($r = 0; $r < count($races); $r++) {
        $stmt->execute(['event_id' => $db->{'e'}, 'race_number' => $races[$r]]);
    }
    header("Location: ./manage.php/?m=1&s=1&e=". $db->{'e'}  );

}
