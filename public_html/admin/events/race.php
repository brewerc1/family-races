<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Manage an Event";
$javascript = "";

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

if (!isset($_GET["r"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$r = isset($_GET["r"]) ? $_GET["r"] : NULL;
$race_number = filter_var($r, FILTER_VALIDATE_INT) ? $r : 0;

$query = "SELECT cancelled FROM race WHERE race_number = :race_number";
$race = $pdo->prepare($query);
if ($race->execute(['race_number' => $race_number])) {
    if ($race->rowCount() > 0) {
        $cancelled = $race->fetch()["cancelled"];
        $cancelled = $cancelled ? 0 : 1;

        $update_query = "UPDATE race SET cancelled = :cancelled WHERE race_number = :race_number";
        $stmt = $pdo->prepare($update_query);
        if ($stmt->execute(["cancelled" => $cancelled, 'race_number' => $race_number])) {

            $query = "SELECT cancelled FROM race WHERE race_number = :race_number";
            $race = $pdo->prepare($query);
            if ($race->execute(['race_number' => $race_number])) {
                if ($race->rowCount() > 0) {
                    $cancelled = $race->fetch()["cancelled"];
                    echo $cancelled ? true : false;
                }
            }

        }
    }

}
