<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
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
            <li><a href="http://localhost/races/index.php">Races</a></li>
            <li><a href="http://localhost/HOF/index.php">HOF</a></li>
            <li><a href="http://localhost/FAQ/index.php">FAQ</a></li>
            <li><a href="http://localhost/profile/index.php">Me</a></li>
            <li><a href="http://localhost/admin/index.php">Admin</a></li>
        </ul>
    
    <footer>
        <p>Created by students of the College of Informatics at Northern Kentucky University</p>
    </footer>
</body>
</html>