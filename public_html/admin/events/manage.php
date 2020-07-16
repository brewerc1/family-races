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
<<<<<<< Updated upstream


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

<h1>Admin Manage an Event Page</h1>
<p>Edit an event, horses, or input race results.</p>

<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>
=======


?>
{header}
{main_nav}
    <h1>Manage an Event</h1>
    <h2>Event Name</h2>
    <h2>Event Date</h2>
    <form>
        <section>
            <label>Jackpot:</label>
            <input type="text" name="Jackpot amount"/>
        </section>
        <section>
            <label>Video:</label>
            <input type="text" name="video"/>
        </section>
    <h5>Races</h5>
        <section>
            <select name="race" id="race">
            <!--number of horses, horse numbers and window status need to be added. form within a form not happening-->
                <option value="race 1">Race 1</option>
                <option value="race 2">Race 2</option>
                <option value="race 3">Race 3</option>
                <option value="race 4">Race 4</option>
                <option value="race 5">Race 5</option>
                <option value="race 6">Race 6</option>
                <option value="race 7">Race 7</option>
                <option value="race 8">Race 8</option>
                <option value="race 9">Race 9</option>
                <option value="race 10">Race 10</option>
                <option value="race 11">Race 11</option>
                <option value="race 12">Race 12</option>
            </select>
        </section>
        <input type="submit" value="Save Event"/>
    </form>
{footer}
<?php ob_end_flush(); ?> 
>>>>>>> Stashed changes
