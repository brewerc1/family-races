<?php
/**
 * Page to Display Site Settings
 * 
 * Page description
 * 
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

$page_title = "Site Settings";
$javascript = '';

// Authentication  and Authorization System
ob_start('template');
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}


?>
{header}
{main_nav}

<main role="main">
        <section id="site_settings">
            <h1>Settings</h1>
            
            <form action="./" method="post">
        
                <p><label>
                    <input type="checkbox" name="sound_fx" <?php //if($_SESSION['sound_fx'] == 1){echo 'checked';} ?>>
                    Sound Effects
                </label></p>

                <p><label>
                    <input type="checkbox" name="voiceovers" <?php //if($_SESSION['voiceovers'] == 1){echo 'checked';} ?>>
                    Voiceovers
                </label></P>

                <p><label>
                    <input type="checkbox" name="terms_enable" <?php //if($_SESSION['voiceovers'] == 1){echo 'checked';} ?>>
                    Enable Terms & Conditions
                </label></P>

                <!-- terms text area -->

                <!-- default horse count select -->

                <p><label>
                    <input type="checkbox" name="memorial_race_enable" <?php //if($_SESSION['voiceovers'] == 1){echo 'checked';} ?>>
                    Enable Memorial Race
                </label></P>

                <!-- Memorial race number (select?) -->

                <!-- Memorial race name text field -->

                <!-- Welcome Video URL text -->

                <!-- Invite Email Subject -->

                <!-- Invite email Body -->


                <input type="submit" value="Save" name="save_button">
            </form>
            
            <p><a href="../" >Cancel</a></p>
            
        </section> <!-- END id user_settings -->
    </main>
{footer}
<?php ob_end_flush(); ?>