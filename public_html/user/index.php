<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/family-races/public_html/bootstrap.php');

//get selected UID
$display_uid = 1; // Replace 1 with $_GET['u']

$display_user_sql = "SELECT * FROM user WHERE id = :display_uid";



?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>User Profile</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <nav id="main-navigation">
        <h1>Main Navigation</h1>
        <ul>
            <li>Menu 1</li>
            <li>Menu 2</li>
            <li>Menu 3</li>
            <li>Menu 4</li>
            <li>Menu 5</li>
        </ul>
    </nav>
    <main role="main">
        <section id="user_info">
            <!-- Assumes DB query returns to $row array-->
            <div>
            <img src="<?php $row['photo'] ?>" alt="User Photo"/>   
            <a href="./edit" class="button">Edit Profile</a> <!-- ??? style into a button or change to form -->
            <a href="" class="button">Go to Google</a> <!-- ??? style into a button or change to form -->
            <p><?php $row['first_name'] . $row['last_name'] ?> </p>\
            </div>
            <div>
                <p><?php $row['motto'] ?></p>
                <p><?php $row['email']  ?></p>
                <p><?php $row['city'] ?></p>
                <p><?php $row['state'] ?></p>
            </div>
        </section>
        
        <section id="user_records">
            <h1>Keenland Records</h1>
            <p><?php $row['records'] ?></p>

        </section>
    </main>
    
    <footer>
        <p>Created by students of the College of Informatics at Northern Kentucky University</p>>
    </footer>
</body>
</html>