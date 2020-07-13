<?php
/**
 * Page to Display User Settings
 *
 * Page displays the current user's settings via checkbox.
 *
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "User Profile";

// include the menu javascript for the template
$javascript = '';

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
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
        <div class="container">
            <section id="user_settings">
                <h1>Settings</h1>

                <form action="./index.php" method="post">
                    <!-- Sound Effects enable -->
                    <div class="form-group toggle">
                        <input class="form-check-input" type="checkbox" id="sound_fx" name="sound_fx" data-toggle="toggle" data-width="75" <?php if($_SESSION['sound_fx'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="sound_fx"> Sound Effects </label>
                    </div>

                    <!-- Voiceovers enable -->
                    <div class="form-group toggle">
                        <input class="form-check-input" type="checkbox" id="voiceovers" name="voiceovers" data-toggle="toggle" data-width="75" <?php if($_SESSION['voiceovers'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="voiceovers"> Voiceovers </label>
                    </div>

                    <button type="submit" class="btn btn-primary" name="save_button">Save</button>
                </form>
                <div id="bottom_links">
                    <a href="./reset.php" >Change Password</a>
                    <a href="/user/" >Cancel</a>
                </div>

            </section> <!-- END id user_settings -->
        </div>
    </main>
    {footer}
<?php ob_end_flush(); ?>