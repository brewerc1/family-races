<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");


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
<h1>Races Page</h1>
<div
    <ul id="race-dropdown">
        <li> Race 1 </li>
        <li> Race 2 </li>
        <li> Race 3 </li>
        <li> Race 4 </li>
        <li> Race 5 </li>
    </ul>

    <div id="betting-open">
        <h2>Place your bet:</h2>
        <form>
            <p>Horse:</p>
            <input type="number" step=1 name="horse-number">
            <p>to</p>
            <ul id="horse-position" name="horse-position">
                <li> Win </li>
                <li> Place </li>
                <li> Show </li>
            </ul>
            <input type="submit" value="Place Bet">
        </form>
    </div>
    <div id="betting-closed">
        <!-- need to add some php to determine whether or not we have an img for the race -->
        <img src="" alt="Default image">
        <h2>The betting window has closed.</h2>
        <h2>Check back for results after the race!</h2>
    </div>
</div>
<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>