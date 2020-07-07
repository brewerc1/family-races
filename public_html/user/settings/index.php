<?php
/**
 * Page to Display User Settings
 * 
 * Page Description
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

$page_title = "User Profile";
$javascript = '';

// Authentication System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

// Get UID
$uid = $_SESSION['id'];

?>
{header}
{main_nav}
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
{footer}
<?php ob_end_flush(); ?>