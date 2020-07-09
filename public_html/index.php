<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Authentication System
ob_start();
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /login/");

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");

} else {
    header("Location: /races/");
}
?>