<?php
/**
 * Page to Display User Settings
 * 
 * Page Description
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Get UID
$uid = $_GET['u']; // All user data, including settings, will be in $_SESSION



// SQL to retrieve user settings
$user_settings_sql = "SELECT sound_fx, voiceovers FROM user WHERE id = :uid";
$user_settings_result = $pdo->prepare($user_settings_sql);
$user_settings_result->execute(['uid' => $uid]);
$num_user_setting_results = $user_settings_result->rowCount();
$row = $user_settings_result->fetch();

$db_sound_fx = $row['sound_fx'];
$db_voiceovers = $row['voiceovers'];

// TODO: create SQL to update preferences



?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>User Settings</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="/css/races.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

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
        <section id="user_settings">
            <h1>Settings</h1>
            <div id="settings">

            <form method="post" action="./?u=<?php echo $uid ?>">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" data-toggle="toggle" name="sound_fx" value="0" id="sound_fx" <?php if($db_sound_fx == 1){echo 'checked';} ?> >
                        Sound Effects
                    </label>
                </div>
                <div class="checkbox disabled">
                    <label>
                        <input type="checkbox" data-toggle="toggle" name="voiceovers" value="0" id="voiceovers" <?php if($db_voiceovers == 1){echo 'checked';} ?>>
                        Voiceovers
                    </label>
                </div>
                <input type="submit" name="save_button" value="Save">
            </form> 
            </div> <!-- END id settings -->

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
            else if ($_POST[]){ $sound_fx_value = 1; }

            // from Chris: if(!isset($_POST['sound_fx']) || $_POST['sound_fx'] != '1'){set to 0}

            
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
