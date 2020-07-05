<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

if (!isset($_GET["email"]) && !isset($_GET["code"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$email = $_GET["email"];
$code = $_GET["code"];

$query = "SELECT email FROM user WHERE pw_reset_code = :pw_reset_code";

$invite_code = $pdo->prepare($query);
$invite_code->execute(['pw_reset_code' => $code]);

if ($invite_code->rowCount() != 1) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}


$sql = "UPDATE user SET pw_reset_code=:pw_reset_code WHERE email=:email";
if (!$pdo->prepare($sql)->execute(['pw_reset_code' => NULL, 'email' => $email])) {
    echo "<h1>Something went wrong</h1>";
} else {
    echo "<h1>Your password reset request was successfully canceled.</h1>";
}


include '../template/footer.php';

