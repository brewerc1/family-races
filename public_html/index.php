<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Authentication System
ob_start();
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
} else {
    header("Location: /races/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

?>