<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Authorization System
// Secure resource: invited user with email and valid code only
if (!isset($_GET["email"]) && !isset($_GET["code"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$email = $_GET["email"];
$code = $_GET["code"];

$query = "SELECT * FROM user WHERE invite_code = :invite_code";

$invite_code = $pdo->prepare($query);
$invite_code->execute(['invite_code' => $code]);

if ($invite_code->rowCount() != 1) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

?>

<h1>Create Account: Step 1</h1>