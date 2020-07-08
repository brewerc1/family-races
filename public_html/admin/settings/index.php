<?php
/**
 * Page to Display Site Settings
 * 
 * Page description
 * 
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "Site Settings";

// include the menu javascript for the template
$javascript = '';


if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$default_horse_selected_tag = '';
$memorial_race_selected_tag = '';

?>
{header}
{main_nav}

<main role="main">
        <section id="site_settings">
            <h1>Settings</h1>
            
            <form action="./" method="post">

                <div class="form-group">
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sound_fx" data-toggle="toggle" name="sound_fx" <?php if($_SESSION['site_sound_fx'] == 1){echo 'checked';} ?>>
                            <label class="form-check-label" for="sound_fx"> Sound Effects <label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="voiceovers" data-toggle="toggle" name="voiceovers" <?php if($_SESSION['site_voiceovers'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="voiceovers"> Voiceovers </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">                     
                        <input class="form-check-input" type="checkbox" id="terms_enable" data-toggle="toggle" name="terms_enable" <?php if($_SESSION['site_terms_enable'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="terms_enable"> Enable Terms & Conditions </label>
                    </div>
                </div>

                <!-- TODO: update to Bootstrap standards --> 
                <!-- terms text area - disabled if terms_enable is 0 -->
                <p><label>
                    <textarea id="terms_enable" name="terms_enable" <?php if($_SESSION['site_terms_enable'] == 0){echo 'disabled';} ?> rows="4" cols="50">
                    <?php echo $_SESSION['site_terms_text'] ?>
                    </textarea>
                    Enable Terms & Conditions
                </label></P>

                <!-- TODO: update to Bootstrap standards --> 
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

                <!-- Memorial race enable -->
                <div class="form-group">
                    <div class="form-check">                        
                        <input class="form-check-input" type="checkbox" id="memorial_race_enable" data-toggle="toggle" name="memorial_race_enable" <?php if($_SESSION['site_memorial_race_enable'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="memorial_race_enable"> Enable Memorial Race </label>
                    </div>
                </div>

                <!-- TODO: update to Bootstrap standards --> 
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
                <div class="form-group row">
                    <div class="col-sm-3">             
                        <input type="text" class="form-control" id="memorial_race_name" name="memorial_race_name" value="<?php echo $_SESSION['site_memorial_race_name'] ?>" <?php if($_SESSION['site_memorial_race_enable'] == 0){echo 'disabled';} ?>>
                    </div>    
                    <label for="memorial_race_name" class="col-sm-3 col-form-label"> Memorial Race Name </label>
                </div>  
                
                
                <!-- Welcome Video URL text -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="welcome_video_url" name="welcome_video_url"  value="<?php echo $_SESSION['site_welcome_video_url'] ?>">
                    </div>
                    <label for="welcome_video_url" class="col-sm-3 col-form-label"> Welcome Video URL </label>
                </div>

                <!-- TODO: update to Bootstrap standards --> 
                <!-- Invite Email Subject -->
                <p><label>
                    <input type="text" name="invite_email_subject" id="invite_email_subject" value="<?php echo $_SESSION['site_invite_email_subject'] ?>">
                    Invite Email Subject
                </label></P>

                <!-- TODO: update to Bootstrap standards --> 
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