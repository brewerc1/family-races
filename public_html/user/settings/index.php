<?php
/**
 * Page to Display User Settings
 * 
 * Page Description
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Get UID
$uid = $_GET['u']; // This should end up coming from $_SESSION

// SQL to retrieve user settings
$user_settings_sql = "SELECT sound_fx, voiceovers FROM user WHERE id = :uid";
$user_settings_result = $pdo->prepare($user_settings_sql);
$user_settings_result->execute(['uid' => $uid]);
$num_user_setting_results = $user_settings_result->rowCount();
$row = $user_settings_result->fetch();

$sound_fx = $row['sound_fx'];
$voiceovers = $row['voiceovers'];

// TODO: create SQL to update preferences

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>User Settings</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function handleClick(cb) {

        }
    </script>
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
            
            <p><label>
                <input type="checkbox" <?php if($sound_fx == 1){echo 'checked';} ?> onclick="handleClick(this);">
                Sound Effects
            </label></p>

            <p><label>
                <input type="checkbox" <?php if($voiceovers == 1){echo 'checked';} ?> onclick="handleClick(this);">
                Voiceovers
            </label></P>
        

            <p>change password link</p>
            <a href="../?u=<?php echo $uid ?>" class="button">Cancel</a>
            <a href="../../password/reset.php" class="button">Change Password</a>
        </section> <!-- END id user_settings -->
    </main>
    
    <footer>
        <p>Created by students of the College of Informatics at Northern Kentucky University</p>
    </footer>
</body>
</html>
