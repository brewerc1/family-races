<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Notification System
$notification = "";
if (isset($_GET["message"]))
    $notification = $_GET["message"];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Forgot Password</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<main role="main">
    <section>
        <h1>Recover Your Password</h1>
        <p>
            To reset your password. enter your account email address. An email will be sent with a reset code.
        </p>
    </section>

    <section>
        <form action="./pw_reset.php" method="POST">
            <input type="email" name="email" placeholder="Email">
            <!--- Notification System : HTML tag may change-->
            <span>
                <?php
                echo $notification;
                ?>
            </span>
            <input type="submit" name="reset_password" value="Reset Password">
        </form>
        <p>
            <a href="http://localhost/login">Return to Log In</a>
        </p>
    </section>
</main>

<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>