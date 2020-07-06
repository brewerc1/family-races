<?php
/**
 * Page to Display User Settings
 * 
 * Page Description
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Authentication System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

// Get UID
$uid = $_SESSION['id'];

// TODO: create SQL to update preferences



?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Skeleton HTML</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
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
        <section id="user_settings">
            <h1>Settings</h1>
            
            <p><label>
                <input type="checkbox" <?php if($_SESSION["sound_fx"]){echo 'checked';} ?> onclick="handleClick(this);">
                Sound Effects
            </label></p>

            <p><label>
                <input type="checkbox" <?php if($_SESSION["voiceovers"]){echo 'checked';} ?> onclick="handleClick(this);">
                Voiceovers
            </label></P>
            
            <a href="../../password/reset.php" class="button">Change Password</a>
            <a href="../?u=<?php echo $uid ?>" class="button">Cancel</a>
            
        </section> <!-- END id user_settings -->
        <section id="testing_area">
        <?php 
        // Check if "save" button was clicked
        if(isset($_POST['save_button'])){
            var_dump($_POST);
            
            //fix: 
            if(!$_POST['sound_fx']){ $sound_fx_value = 0; } 
            // else if ($_POST[]){ $sound_fx_value = 1; }

            // from Chris: if(!isset($_POST['sound_fx']) || $_POST['sound_fx'] != '1'){set to 0}
            //if(!isset($_POST['sound_fx']) || $_POST['sound_fx'] != '1'){set to 0}

            
            //do the same for voiceovers
            if(!$_POST['voiceovers']){ $voiceover_value = 0; } else { $voiceovers_value = 1; }


            $voiceovers_value = $_POST['voiceovers'];

            $update_preferences_sql = "UPDATE user SET sound_fx = :sound_fx_value, voiceovers = :voiceovers_value  WHERE id = :uid;";
            $update_preferences_result = $pdo->prepare($update_preferences_sql);
            $update_preferences_result->execute(['sound_fx_value' => $sound_fx_value, 'voiceovers_value' => $voiceovers_value, 'uid' => $uid]);
            $row = $user_settings_result->fetch();
            

        }
        ?>
        </section>
    </main>

<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>
