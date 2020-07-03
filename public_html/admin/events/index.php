<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication  and Authorization System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

// To be reviewed
if (!$_SESSION["admin"])
    header("Location: " . $_SERVER["HTTP_REFERER"] . "?access=denied");

?>

<h1>Admin Events Page</h1>
<p>Displays a list of events</p>