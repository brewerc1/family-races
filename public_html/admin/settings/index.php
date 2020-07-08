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
$default_horse_selected_tag = '';
$memorial_race_selected_tag = '';

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

// SQL to get site settings - needs to exist in bootstrap with conditional (!$_SESSION['id])
    $site_settings_sql = "SELECT * FROM site_settings";
    $site_settings_result = $pdo->prepare($site_settings_sql);
    $site_settings_result->execute();
    $row = $site_settings_result->fetch();

    $_SESSION['site_sound_fx'] = $row['sound_fx'];
    $_SESSION['site_voiceovers'] = $row['voiceovers'];
    $_SESSION['site_terms_enable'] = $row['terms_enable'];
    $_SESSION['site_terms_text'] = $row['terms_text'];
    $_SESSION['site_default_horse_count'] = $row['default_horse_count'];
    $_SESSION['site_memorial_race_enable'] = $row['memorial_race_enable'];
    $_SESSION['site_memorial_race_number'] = $row['memorial_race_number'];
    $_SESSION['site_memorial_race_name'] = $row['memorial_race_name'];
    $_SESSION['site_welcome_video_url'] = $row['welcome_video_url'];
    $_SESSION['site_invite_email_subject'] = $row['invite_email_subject'];
    $_SESSION['site_invite_email_body'] = $row['invite_email_body'];


?>
{header}
{main_nav}

<main role="main">
        <section id="site_settings">
            <h1>Settings</h1>
            
            <form action="./" method="post">
        
                <p><label>
                    <input type="checkbox" data-toggle="toggle" name="sound_fx" <?php if($_SESSION['site_sound_fx'] == 1){echo 'checked';} ?>>
                    Sound Effects
                </label></p>

                <p><label>
                    <input type="checkbox" data-toggle="toggle" name="voiceovers" <?php if($_SESSION['site_voiceovers'] == 1){echo 'checked';} ?>>
                    Voiceovers
                </label></P>

                <p><label>
                    <input type="checkbox" data-toggle="toggle" name="terms_enable" <?php if($_SESSION['site_terms_enable'] == 1){echo 'checked';} ?>>
                    Enable Terms & Conditions
                </label></P>

                <!-- terms text area - disabled if terms_enable is 0 -->
                <p><label>
                    <textarea id="terms_enable" name="terms_enable" <?php if($_SESSION['site_terms_enable'] == 0){echo 'disabled';} ?> rows="4" cols="50">
                    <?php echo $_SESSION['site_terms_text'] ?>
                    </textarea>
                    Enable Terms & Conditions
                </label></P>

                <!-- default horse count select -->
                <p><label>
                    <select id="default_horse_count">
                        <?php 
                        for ($i=1; $i <= 16; $i++) { 
                            if($_SESSION['site_default_horse_count'] == $i){
                                $default_horse_selected_tag = "selected";
                            } else {
                                $default_horse_selected_tag = "";
                            }
echo <<<ENDOPTION
                        <option value="$i" $default_horse_selected_tag>$i</option>
ENDOPTION;
                        }
                        ?>
                    </select>
                    Default Horse Count
                </label></P>

                <p><label>
                    <input type="checkbox" data-toggle="toggle" name="memorial_race_enable" <?php if($_SESSION['site_memorial_race_enable'] == 1){echo 'checked';} ?>>
                    Enable Memorial Race
                </label></P>

                <!-- Memorial race number (select?) -->
                <p><label>
                    <select id="memorial_race_number" <?php if($_SESSION['site_memorial_race_enable'] == 0){echo 'disabled';} ?>>
                        <?php 
                        for ($i=1; $i <= 16; $i++) { 
                            if($_SESSION['site_memorial_race_number'] == $i){
                                $memorial_race_selected_tag = "selected";
                            } else {
                                $memorial_race_selected_tag = "";
                            }
echo <<<ENDOPTION
                        <option value="$i" $memorial_race_selected_tag>$i</option>
ENDOPTION;
                        }
                        ?>
                    </select>
                    Memorial Race Number
                </label></P>


                <!-- Memorial race name text field -->
                <p><label>
                    <input type="text" name="memorial_race_name" id="memorial_race_name" value="<?php echo $_SESSION['site_memorial_race_name'] ?>" <?php if($_SESSION['site_memorial_race_enable'] == 0){echo 'disabled';} ?>>
                    Memorial Race Name
                </label></P>

                <!-- Welcome Video URL text -->
                <p><label>
                    <input type="text" name="welcome_video_url" id="welcome_video_url" value="<?php echo $_SESSION['site_welcome_video_url'] ?>">
                    Welcome Video URL
                </label></P>

                <!-- Invite Email Subject -->
                <p><label>
                    <input type="text" name="invite_email_subject" id="invite_email_subject" value="<?php echo $_SESSION['site_invite_email_subject'] ?>">
                    Invite Email Subject
                </label></P>

                <!-- Invite email Body -->
                <p><label>
                    <textarea id="invite_email_body" name="invite_email_body" rows="4" cols="50">
                    <?php echo $_SESSION['site_invite_email_body'] ?>
                    </textarea>
                    Invite Email Body
                </label></P>

                <input type="submit" value="Save" name="save_button">
            </form>
            
            <p><a href="../" >Cancel</a></p>
            
        </section> <!-- END id user_settings -->
        <section id="testing">
        <?php
/*
        echo $_SESSION['site_sound_fx'] . 
        $_SESSION['site_voiceovers'] . 
        $_SESSION['site_terms_enable'] . 
        $_SESSION['site_terms_text'] . 
        $_SESSION['site_default_horse_count'] . 
        $_SESSION['site_memorial_race_enable'] . 
        $_SESSION['site_memorial_race_number'] . 
        $_SESSION['site_memorial_race_name']  . 
        $_SESSION['site_welcome_video_url'] . 
        $_SESSION['site_invite_email_subject'] . 
        $_SESSION['site_invite_email_body'] 
*/

        ?>
        </section>
    </main>
{footer}
<?php ob_end_flush(); ?>