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
//$uid = $_SESSION["id"]; // This should end up coming from $_SESSION



// TODO: create SQL to update preferences: Done

/**
 * @param integer $pref_val : can be either 0  or 1
 * @param string $pref_name : $_GET["changePref"]
 * @param PDO $pdo
 * @author makungaj1
 */
function updatePreferences($pref_val, $pref_name, PDO $pdo) {
    // Update the Session variable
    $_SESSION[$pref_name] = ($pref_val == 1);

    // value to be set in DB
    $val = $_SESSION[$pref_name] ? 1 : NULL;

    // sql prepared stmt
    if ($pref_name == "sound_fx") {
        $sql = "UPDATE user SET sound_fx=:val WHERE id=:id";
    } else {
        $sql = "UPDATE user SET voiceovers=:val WHERE id=:id";
    }

    $pdo->prepare($sql)->execute(['val' => $val, 'id' => $_SESSION["id"]]);

    // redirect user to user/settings to avoid bugs on page reload
    header("Location: http://localhost/user/settings/");
}

// run only if $_GET["changePref"] is set
if (isset($_GET["changePref"])) {
    $val = $_SESSION[$_GET["changePref"]] ? 0 : 1;
    updatePreferences($val, $_GET["changePref"], $pdo);
}


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
    <script>
        /**
         * @param prefN, can be either sound_fx or voiceovers
         * @author makungaj1
         *
         *  Upon onClick event, the script redirect to user/settings/?changePre= sound_fx of voiceovers
         *  so that the php updatePreferences() function will catch it in $_GET["changePref"]
         * */
        function handleOnClick(prefN) {
            window.location.replace("http://localhost/user/settings/?changePref=" + prefN);
        }
    </script>
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
                <input type="checkbox" <?php if($_SESSION["sound_fx"]){echo 'checked';} ?> onclick="handleOnClick('sound_fx');">
                Sound Effects
            </label></p>

            <p><label>
                <input type="checkbox" <?php if($_SESSION["voiceovers"]){echo 'checked';} ?> onclick="handleOnClick('voiceovers');">
                Voiceovers
            </label></P>
            
            <a href="../../password/reset.php" class="button">Change Password</a>
            <a href="../" class="button">Cancel</a>
            
        </section> <!-- END id user_settings -->
        <section id="testing_area">
        <?php 
        // Check if "save" button was clicked
        if(isset($_POST['save_button'])){
//            var_dump($_POST);
//
//            //fix:
//            if(!$_POST['sound_fx']){ $sound_fx_value = 0; }
//            // else if ($_POST[]){ $sound_fx_value = 1; }
//
//            //if(!isset($_POST['sound_fx']) || $_POST['sound_fx'] != '1'){set to 0}
//
//            if(!$_POST['voiceovers']){ $voiceover_value = 0; } else { $voiceovers_value = 1; }
//
//
//            $voiceovers_value = $_POST['voiceovers'];
//
//            $update_preferences_sql = "UPDATE user SET sound_fx = :sound_fx_value, voiceovers = :voiceovers_value  WHERE id = :uid;";
//            $update_preferences_result = $pdo->prepare($update_preferences_sql);
//            $update_preferences_result->execute(['sound_fx_value' => $sound_fx_value, 'voiceovers_value' => $voiceovers_value, 'uid' => $uid]);
//            $row = $user_settings_result->fetch();
            

        }
        ?>
        </section>
    </main>

<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>
