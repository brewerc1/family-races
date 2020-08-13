<?php
/**
 * Page to Logout the User
 * 
 * This page logs out the user by: 
 * - Unsetting the session keys;
 * - Expiring the session cookie in the user's browser;
 * - Destroying the session.
 */
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

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

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: /");
exit;
