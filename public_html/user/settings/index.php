<?php
/**
 * Page to Display User Settings
 *
 * Page displays the current user's settings and allows the user to change
 * certain sitewide settings.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// set the page title for the template
$page_title = "User Settings";

$debug = debug($_POST);

// include the menu javascript for the template
$javascript = <<< JAVASCRIPT
$("form").submit(function() {
    $("#sound_fx").removeAttr("disabled");
    $("#voiceovers").removeAttr("disabled");
});
JAVASCRIPT;

if (empty($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// Get UID
$uid = filter_var(trim($_SESSION['id']), FILTER_SANITIZE_NUMBER_INT);

// Check if "save" button was clicked
if(isset($_POST['save_button'])){

// Sound Effects Enable
    if(!empty($_POST['sound_fx']) && $_POST['sound_fx'] == 'on'){
        $sound_fx_value = 1;
    } else {
        $sound_fx_value = 0;
    }

    // Voiceovers Enable
    if(!empty($_POST['voiceovers']) && $_POST['voiceovers'] == 'on'){
        $voiceovers_value = 1;
    } else {
        $voiceovers_value = 0;
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

        // confirm update
        header("Location: ".$_SERVER['PHP_SELF']."?m=15&s=success");
    }
}
?>
    {header}
    {main_nav}
    <main role="main" id="admin_site_settings">
		<h1 class="my-5 sticky-top">Settings</h1>
		<section>
            <form class="my-5" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <!-- Sound Effects enable -->
                <div class="form-group custom-control custom-switch custom-switch-lg">
                    <input class="custom-control-input" type="checkbox" id="sound_fx" name="sound_fx" <?php if($_SESSION['sound_fx'] == 1){echo 'checked';}?><?php if ($_SESSION['site_sound_fx'] == 0){echo ' disabled';} ?>>
                    <label class="custom-control-label" for="sound_fx"> Sound Effects </label>
                </div>

                <!-- Voiceovers enable -->
                <div class="form-group custom-control custom-switch custom-switch-lg">
                    <input class="custom-control-input" type="checkbox" id="voiceovers" name="voiceovers" <?php if($_SESSION['voiceovers'] == 1){echo 'checked';} ?><?php if ($_SESSION['site_voiceovers'] == 0){echo ' disabled';} ?>>
                    <label class="custom-control-label" for="voiceovers"> Voiceovers </label>
                </div>

                <div class="col text-center">
                    <button type="submit" class="btn btn-primary btn col-sm-5" name="save_button">Save</button>
                    <a href="/user/index.php" class="btn btn-text d-block mt-2 text-center">Cancel</a>
                </div>
            </form>
        </section> <!-- END id user_settings -->
    </main>
    {footer}
<?php ob_end_flush(); ?>
