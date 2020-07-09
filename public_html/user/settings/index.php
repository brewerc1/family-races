<?php
/**
 * Page to Display User Settings
 * 
 * Page displays the current user's settings via checkbox.
 * 
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

$page_title = "User Profile";
$javascript = '';

// Authentication System
ob_start('template');
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /login/");

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");

}

// Get UID
$uid = $_SESSION['id'];

// Check if "save" button was clicked
if(isset($_POST['save_button'])){

    // If the sound_fx variable was not sent, or is not what is expected, set to 0
    if(!isset($_POST['sound_fx']) || $_POST['sound_fx'] != 'on'){
        $sound_fx_value = 0;
    } else {
        $sound_fx_value = 1;
    }
    // Same process for voiceovers. Any future 'boolean' settings follow this logic
    if(!isset($_POST['voiceovers']) || $_POST['voiceovers'] != 'on'){
        $voiceovers_value = 0;
    } else {
        $voiceovers_value = 1;
    }

    // PDO to update the DB 
    $update_preferences_sql = "UPDATE user SET sound_fx = :sound_fx_value, voiceovers = :voiceovers_value  WHERE id = :uid";
    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute(['sound_fx_value' => $sound_fx_value, 'voiceovers_value' => $voiceovers_value, 'uid' => $uid]);
    
    //requery DB to update $_SESSION. Ensures current settings are always displayed
    if ($update_preferences_result){    
    $update_session_sql = "SELECT sound_fx, voiceovers FROM user WHERE id = :uid";
    $update_session_result = $pdo->prepare($update_session_sql);
    $update_session_result->execute(['uid' => $uid]);
    $row = $update_session_result->fetch();
    
    $_SESSION['sound_fx'] = $row['sound_fx'];
    $_SESSION['voiceovers'] = $row['voiceovers'];
    }
}
?>
{header}
{main_nav}
    <main role="main">
        <section id="user_settings">
            <h1>Settings</h1>
            
            <form action="./index.php" method="post">
            <div class="checkbox"><p><label>
                    <input type="checkbox" data-toggle="toggle" name="sound_fx" <?php if($_SESSION['sound_fx'] == 1){echo 'checked';} ?>>
                    Sound Effects
                </label></p></div>

                <p><label>
                    <input type="checkbox" data-toggle="toggle" name="voiceovers" <?php if($_SESSION['voiceovers'] == 1){echo 'checked';} ?>>
                    Voiceovers
                </label></P>
                <input type="submit" value="Save" name="save_button">
            </form>
            <p><a href="../../password/reset.php" >Change Password</a></p>
            <p><a href="../?u=<?php echo $uid ?>" >Cancel</a></p>
            
        </section> <!-- END id user_settings -->
    </main>
{footer}
<?php ob_end_flush(); ?>