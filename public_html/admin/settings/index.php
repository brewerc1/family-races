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
    <div class="container">
        <section id="site_settings">
            <h1>Settings</h1>
            
            <form action="./index.php" method="post">
                <!-- Sound Effects enable -->
                <div class="form-group">
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sound_fx" name="sound_fx" data-toggle="toggle" <?php if($_SESSION['site_sound_fx'] == 1){echo 'checked';} ?>>
                            <label class="form-check-label" for="sound_fx"> Sound Effects <label>
                    </div>
                </div>
                <!-- Voiceovers enable -->
                <div class="form-group">
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="voiceovers" name="voiceovers" data-toggle="toggle" <?php if($_SESSION['site_voiceovers'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="voiceovers"> Voiceovers </label>
                    </div>
                </div>
                <!-- Terms and Condistions enable -->
                <div class="form-group">
                    <div class="form-check">                     
                        <input class="form-check-input" type="checkbox" id="terms_enable" name="terms_enable" data-toggle="toggle" <?php if($_SESSION['site_terms_enable'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="terms_enable"> Enable Terms & Conditions </label>
                    </div>
                </div>

                <!-- terms text area - disabled if terms_enable is 0 -->
                <div class="form-group row">
                    <div class="col">
                        <textarea class="form-control" id="terms_enable_text" name="terms_enable_text" <?php if($_SESSION['site_terms_enable'] == 0){echo 'disabled';} ?> rows="4" >
                        <?php echo $_SESSION['site_terms_text'] ?> </textarea>
                    </div>
                    <label for="terms_enable_text" class="col-form-label"> Enable Terms & Conditions </label>
                </div>

                <!-- default horse count select -->
                <div class="form-group row">
                    <div class="col">
                        <select id="default_horse_count" class="form-control">
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
                    </div>
                    <label for="default_horse_count" class="col-form-label"> Default Horse Count </label>
                </div>

                <!-- Memorial race enable -->
                <div class="form-group">
                    <div class="form-check">                        
                        <input class="form-check-input" type="checkbox" id="memorial_race_enable" name="memorial_race_enable" data-toggle="toggle" <?php if($_SESSION['site_memorial_race_enable'] == 1){echo 'checked';} ?>>
                        <label class="form-check-label" for="memorial_race_enable"> Enable Memorial Race </label>
                    </div>
                </div>

                <!-- Memorial race number -->
                <div class="form-group row">
                    <div class="col">
                        <select id="memorial_race_number" class="form-control" <?php if($_SESSION['site_memorial_race_enable'] == 0){echo 'disabled';} ?>>
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
                    <label for="memorial_race_number" class="col-form-label"> Race Number </label>
               
                <!-- Memorial race name text field -->
                <div class="form-group row">
                    <div class="col">             
                        <input type="text" class="form-control" id="memorial_race_name" name="memorial_race_name" value="<?php echo $_SESSION['site_memorial_race_name'] ?>" <?php if($_SESSION['site_memorial_race_enable'] == 0){echo 'disabled';} ?>>
                    </div>    
                    <label for="memorial_race_name" class="col-form-label"> Memorial Race Name </label>
                </div>  

                <!-- Welcome Video URL text -->
                <div class="form-group row">
                    <div class="col">
                        <input type="text" class="form-control" id="welcome_video_url" name="welcome_video_url"  value="<?php echo $_SESSION['site_welcome_video_url'] ?>">
                    </div>
                    <label for="welcome_video_url" class="col-form-label"> Welcome Video URL </label>
                </div>
      
                <!-- Invite Email Subject -->
                <div class="form-group row">
                    <div class="col"> 
                        <input type="text" class="form-control" id="invite_email_subject" name="invite_email_subject" value="<?php echo $_SESSION['site_invite_email_subject'] ?>">
                        <label for="invite_email_subject" class="col-form-label"> Invite Email Subject </label>
                    </div>
                </div>

                <!-- Invite email Body -->
                <div class="form-group row">
                    <div class="col">
                        <textarea class="form-control" id="invite_email_body" name="invite_email_body" rows="4">
                        <?php echo $_SESSION['site_invite_email_body'] ?></textarea>
                    </div>
                    <label for="invite_email_body" class="col-form-label"> Email Body </label>
                </div>

                <!-- Invite email server port -->
                <div class="form-group row">
                    <div class="col"> 
                        <input type="text" class="form-control" id="invite_email_server_port" name="invite_email_server_port" value="<?php echo $_SESSION["site_email_server_port"] ?>">
                        <label for="invite_email_server_port" class="col-form-label"> Invite Email Server Port </label>
                    </div>
                </div>

                <!-- Invite email server account -->
                <div class="form-group row">
                    <div class="col"> 
                        <input type="text" class="form-control" id="invite_email_server_account" name="invite_email_server_account" value="<?php echo $_SESSION["site_email_server_account"] ?>">
                        <label for="invite_email_server_account" class="col-form-label"> Invite Email Server Account </label>
                    </div>
                </div>

                <!-- Invite email server password -->
                <div class="form-group row">
                    <div class="col"> 
                        <input type="text" class="form-control" id="invite_email_server_password" name="invite_email_server_password" value="<?php echo $_SESSION["site_email_server_password"] ?>">
                        <label for="invite_email_server_password" class="col-form-label"> Invite Email Server Password </label>
                    </div>
                </div>

                <!-- Invite email from name -->
                <div class="form-group row">
                    <div class="col"> 
                        <input type="text" class="form-control" id="invite_email_from_name" name="invite_email_from_name" value="<?php echo $_SESSION["site_email_from_name"] ?>">
                        <label for="invite_email_from_name" class="col-form-label"> Invite Email From Name </label>
                    </div>
                </div>

                <!-- Invite email from address -->
                <div class="form-group row">
                    <div class="col"> 
                        <input type="text" class="form-control" id="invite_email_from_address" name="invite_email_from_address" value="<?php echo $_SESSION["site_email_from_address"] ?>">
                        <label for="invite_email_from_address" class="col-form-label"> Invite Email From Address </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" name="save_button">Save</button>
            </form>     

            <p><a href="../" >Cancel</a></p>
        </section> <!-- END id user_settings -->
    </div>
</main>
{footer}
<?php ob_end_flush(); ?>