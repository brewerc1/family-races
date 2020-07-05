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
$uid = $_SESSION["id"]; // This should end up coming from $_SESSION

// SQL to retrieve user settings
$user_settings_sql = "SELECT sound_fx, voiceovers FROM user WHERE id = :uid";
$user_settings_result = $pdo->prepare($user_settings_sql);
$user_settings_result->execute(['uid' => $uid]);
$num_user_setting_results = $user_settings_result->rowCount();
$row = $user_settings_result->fetch();

$sound_fx = $row['sound_fx'];
$voiceovers = $row['voiceovers'];

// TODO: create SQL to update preferences

include '../../template/header.php';
?>
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

<?php
include '../../template/footer.php';
?>
