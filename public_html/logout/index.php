<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication System
ob_start();
session_start();

session_destroy();

// unset session variables
unset($_SESSION["id"]);
unset($_SESSION["first_name"]);
unset($_SESSION["last_name"]);
unset($_SESSION["email"]);
unset($_SESSION["create_time"]);
unset($_SESSION["update_time"]);
unset($_SESSION["city"]);
unset($_SESSION["state"]);
unset($_SESSION["motto"]);
unset($_SESSION["photo"]);
unset($_SESSION["sound_fx"]);
unset($_SESSION["voiceovers"]);
unset($_SESSION["admin"]);

// Clean (erase) the output buffer and turn off output buffering
ob_end_clean();

header("Location: /");

