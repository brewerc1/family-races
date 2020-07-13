<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication  and Authorization System
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
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

?>
<!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
        <title>Skeleton HTML</title>

        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
        <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
        <link href="/css/races.css" rel="stylesheet">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <style>
            nav#main-navigation li {
                display: inline-block;
                width: 18%;
            }
            nav#main-navigation ul {
                margin:0;
                padding:0;
            }
        </style>
    </head>
<body>
<!--The main navigation menu to be displayed on most pages. Not all links work yet.-->
<nav id="main-navigation">
    <h1>Main Navigation</h1>
    <ul>
        <li><a href="http://localhost/races">Races</a></li>
        <li><a href="http://localhost/HOF/">HOF</a></li>
        <li><a href="http://localhost/faq/">FAQ</a></li>
        <li><a href="http://localhost/user/">Me</a></li>
        <?php
        if ($_SESSION['admin']) {
            echo <<< ADMIN
<li><a href= "http://localhost/admin/">Admin</a></li>
ADMIN;
        }
        ?>
        <li><a href="http://localhost/logout">Log out</a></li>
    </ul>
</nav>
    <main role="main">
        <section>
            <h1>The Stables</h1>
            <ul >
                <li><a href="link to current event"> Current Event </a></li>
                <li><a href="link to event and race management page">Event & Race Managment</a></li>
                <li><a href="./users">User Management</a></li>
                <li><a href="./settings">Site Settings</a></li>
            </ul>
        </section>
     
    </main>

<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>