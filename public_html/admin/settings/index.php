<?php
/**
 * Page to Display Site Settings
 * 
 * Page displays and allows access to admin level settings.
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

if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

///// DEBUG
//$debug = debug($_POST);
///// end DEBUG

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

    // Site Name Text
    if(!empty($_POST['site_name'])){
        $site_name_value = trim($_POST['site_name']);
    } else {
        $site_name_value = $_SESSION['site_name'];
    }

    // Terms and Conditions
    if(!empty($_POST['terms_enable']) && $_POST['terms_enable'] == 'on'){
        $terms_enable_value = 1;
    } else {
        $terms_enable_value = 0;
    }

    // Terms and Conditions Text
    if(!empty($_POST['terms_text'])){
        $terms_text_value = trim($_POST['terms_text']);
    } else {
        $terms_text_value = $_SESSION['site_terms_text'];
    }
    
    // Default Horse Count
    if(!empty($_POST['default_horse_count']) && (int)$_POST['default_horse_count'] == $_POST['default_horse_count']){
        $default_horse_count_value = $_POST['default_horse_count'];
    } else {
        $default_horse_count_value = $_SESSION['site_default_horse_count'];
    }

    // Memorial Race Enable
    if(!empty($_POST['memorial_race_enable']) && $_POST['memorial_race_enable'] == 'on'){
        $memorial_race_enable_value = 1;
    } else {
        $memorial_race_enable_value = 0;
    }
        
    // Memorial Race Number
    if(!empty($_POST['memorial_race_number']) && (int)$_POST['memorial_race_number'] == $_POST['memorial_race_number']){
        $memorial_race_number_value = $_POST['memorial_race_number'];
    } else {
        $memorial_race_number_value = $_SESSION['site_memorial_race_number'];
    }

    // Memorial Race Name Text
    if(!empty($_POST['memorial_race_name'])){
        $memorial_race_name_value = trim($_POST['memorial_race_name']);
    } else {
        $memorial_race_name_value = $_SESSION['site_memorial_race_name'];
    }

    // Welcome Video URL Text
    if(!empty($_POST['welcome_video_url'])){
        $welcome_video_url_value = trim($_POST['welcome_video_url']);
    } else {
        $welcome_video_url_value = $_SESSION['site_welcome_video_url'];
    }

    // Invite Email Subject Text
    if(!empty($_POST['invite_email_subject'])){
        $invite_email_subject_value = trim($_POST['invite_email_subject']);
    } else {
        $invite_email_subject_value = $_SESSION['site_invite_email_subject'];
    }

    // Invite Email Body Text
    if(!empty($_POST['invite_email_body'])){
        $invite_email_body_value = trim($_POST['invite_email_body']);
    } else {
        $invite_email_body_value = $_SESSION['site_invite_email_body'];
    }

    // Invite Email Server Text
    if(!empty($_POST['email_server'])){
        $email_server_value = trim($_POST['email_server']);
    } else {
        $email_server_value = $_SESSION['site_email_server'];
    }

    // Invite Email Server Port Text
    if(!empty($_POST['email_server_port'])){
        $email_server_port_value = trim($_POST['email_server_port']);
    } else {
        $email_server_port_value = $_SESSION['site_email_server_port'];
    }

    // Invite Email Server Account Text
    if(!empty($_POST['email_server_account'])){
        $email_server_account_value = filter_var(trim($_POST['email_server_account']), FILTER_VALIDATE_EMAIL);
    } else {
        $email_server_account_value = $_SESSION['site_email_server_account'];
    }

    // Invite Email Server Password Text
    if(!empty($_POST['email_server_password'])){
        $email_server_password_value = trim($_POST['email_server_password']);
    } else {
        $email_server_password_value = $_SESSION['site_email_server_password'];
    }

    // Invite Email From Name Text
    if(!empty($_POST['email_from_name'])){
        $email_from_name_value = trim($_POST['email_from_name']);
    } else {
        $email_from_name_value = $_SESSION['site_email_from_name'];
    }
   
    // Invite Email From Address Text
    if(!empty($_POST['email_from_address'])){
        $email_from_address_value = trim($_POST['email_from_address']);
    } else {
        $email_from_address_value = $_SESSION['site_email_from_address'];
    }

    // PDO to update the DB 
    $update_preferences_sql = 
    
    "UPDATE site_settings SET 
    sound_fx = :sound_fx_value, voiceovers = :voiceovers_value, terms_enable = :terms_enable_value,
    terms_text = :terms_text_value, default_horse_count = :default_horse_count_value, memorial_race_enable = :memorial_race_enable_value,
    memorial_race_number = :memorial_race_number_value, memorial_race_name = :memorial_race_name_value, welcome_video_url = :welcome_video_url_value,
    invite_email_subject = :invite_email_subject_value, invite_email_body = :invite_email_body_value, email_server = :email_server_value,
    email_server_port = :email_server_port_value, email_server_account = :email_server_account_value, email_server_password = :email_server_password_value,
    email_from_name = :email_from_name_value, email_from_address = :email_from_address_value, name = :site_name_value
    WHERE id = 1";

    $update_preferences_result = $pdo->prepare($update_preferences_sql);
    $update_preferences_result->execute(['sound_fx_value' => $sound_fx_value, 'voiceovers_value' => $voiceovers_value, 'terms_enable_value' => $terms_enable_value, 
        'terms_text_value' => $terms_text_value, 'default_horse_count_value' => $default_horse_count_value, 'memorial_race_enable_value' => $memorial_race_enable_value,
        'memorial_race_number_value' => $memorial_race_number_value, 'memorial_race_name_value' => $memorial_race_name_value, 'welcome_video_url_value' => $welcome_video_url_value,
        'invite_email_subject_value' => $invite_email_subject_value, 'invite_email_body_value' => $invite_email_body_value, 'email_server_value' => $email_server_value,
        'email_server_port_value' => $email_server_port_value, 'email_server_account_value' => $email_server_account_value, 'email_server_password_value' => $email_server_password_value,
        'email_from_name_value' => $email_from_name_value, 'email_from_address_value' => $email_from_address_value, 'site_name_value' => $site_name_value]);
    
    //requery DB to update $_SESSION. Ensures current settings are always displayed
    if ($update_preferences_result){    
        $update_session_sql = "SELECT * FROM site_settings";
        $update_session_result = $pdo->prepare($update_session_sql);
        $update_session_result->execute();

        if ($update_session_result->rowCount() > 0){
            $row = $update_session_result->fetch();
            foreach ($row as $site_key => $site_value){
                $_SESSION["site_" . $site_key] = $site_value;
            }
        }
        // confirm update
        header("Location: ".$_SERVER["PHP_SELF"]."?m=15&s=success");
    }
} // END POST variable check

// HTML tag declarations
$default_horse_selected_tag = '';
$memorial_race_selected_tag = '';

?>
{header}
{main_nav}

<main role="main" id="admin_site_settings">
	<h1 class="mb-5 sticky-top">Site Settings</h1>
    <section id="site_settings">
            
        <form class="mt-5" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#general">General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#memorial">Memorial</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#terms">Terms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#email">Email</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane show active" id="general">
                    <!-- Site name text field -->
                    <div class="form-group row">
                        <div class="col">
                            <label for="memorial_race_name" class="col-form-label"> Site Name </label>          
                            <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo $_SESSION['site_name'] ?>">
                        </div>    
                    </div>

                    <!-- Welcome Video URL text -->
                    <div class="form-group row">
                        <div class="col">
                            <label for="welcome_video_url" class="col-form-label"> Welcome Video URL </label>    
                            <input type="text" class="form-control" id="welcome_video_url" name="welcome_video_url"  value="<?php echo $_SESSION['site_welcome_video_url'] ?>">
                        </div>
                    </div>

                    <!-- default horse count select -->
                    <div class="form-group row">
                        <div class="col">
                            <label for="default_horse_count" class="col-form-label"> Default Horse Count </label>
                            <select id="default_horse_count" name="default_horse_count"class="form-control">
                                <?php 
                                for ($i=1; $i <= 21; $i++) { 
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
                    </div>
                    
                    <!-- Voiceovers enable -->
                    <div class="form-group custom-control custom-switch custom-switch-lg">
                        <input class="custom-control-input" type="checkbox" id="voiceovers" name="voiceovers" <?php if($_SESSION['site_voiceovers'] == 1){echo 'checked';} ?>>
                        <label class="custom-control-label" for="voiceovers"> Voiceovers </label>
                    </div>

                    <!-- Sound Effects enable -->
                    <div class="form-group custom-control custom-switch custom-switch-lg">
                        <input class="custom-control-input" type="checkbox" id="sound_fx" name="sound_fx" <?php if($_SESSION['site_sound_fx'] == 1){echo 'checked';} ?>>             
                        <label class="custom-control-label" for="sound_fx"> Sound Effects </label>
                    </div>

                    
                </div> <!-- END General Tab -->

                <div class="tab-pane" id="memorial">
                    <!-- Memorial race enable -->
                    <div class="form-group custom-control custom-switch custom-switch-lg">
                        <input class="custom-control-input" type="checkbox" id="memorial_race_enable" name="memorial_race_enable" <?php if($_SESSION['site_memorial_race_enable'] == 1){echo 'checked';} ?>>
                        <label class="custom-control-label" for="memorial_race_enable"> Enable Memorial Race </label>
                    </div>

                    <!-- Memorial race number -->
                    <div class="form-group row">
                        <div class="col">
                            <label for="memorial_race_number" class="col-form-label"> Memorial Race Number </label>
                            <select id="memorial_race_number" name="memorial_race_number" class="form-control" <?php if($_SESSION['site_memorial_race_enable'] == 0){echo 'disabled';} ?>>
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
                        </div>
                    </div>
                    <!-- Memorial race name text field -->
                    <div class="form-group row">
                        <div class="col">
                            <label for="memorial_race_name" class="col-form-label"> Memorial Race Name </label>          
                            <input type="text" class="form-control" id="memorial_race_name" name="memorial_race_name" value="<?php echo $_SESSION['site_memorial_race_name'] ?>" <?php if($_SESSION['site_memorial_race_enable'] == 0){echo 'disabled';} ?>>
                        </div>    
                    </div>

                </div><!-- END Memorial Tab -->

                <div class="tab-pane" id="terms">
                    
                    <!-- Terms and Conditions enable -->
                    <div class="form-group custom-control custom-switch custom-switch-lg">
                        <input class="custom-control-input" type="checkbox" id="terms_enable" name="terms_enable" <?php if($_SESSION['site_terms_enable'] == 1){echo 'checked';} ?>>
                        <label class="custom-control-label" for="terms_enable"> Enable Terms & Conditions </label>
                    </div>

                    <!-- terms text area - disabled if terms_enable is 0 -->
                    <div class="form-group row">
                        <div class="col">
                            <label for="terms_text" class="sr-only"> Terms & Conditions </label>
                            <textarea class="form-control" id="terms_text" name="terms_text" <?php if($_SESSION['site_terms_enable'] == 0){echo 'disabled';} ?> rows="8" ><?php if(!empty($_SESSION['site_terms_text'])) echo $_SESSION['site_terms_text']; ?></textarea>
                        </div>
                    </div>

                </div><!-- END Terms Tab -->

                <div class="tab-pane" id="email">
                    <div class="form-row">
                        <!-- Invite Email Subject -->
                        <div class="form-group">
                            <div class="col"> 
                                <label for="invite_email_subject" class="col-form-label"> Invite Email Subject </label>    
                                <input type="text" class="form-control" id="invite_email_subject" name="invite_email_subject" value="<?php echo $_SESSION['site_invite_email_subject'] ?>">   
                            </div>
                        </div>

                        <!-- Invite Email Body -->
                        <div class="form-group">
                            <div class="col">
                                <label for="invite_email_body" class="col-form-label"> Invite Email Body </label>
                                <textarea class="form-control" id="invite_email_body" name="invite_email_body" rows="4"><?php if(!empty($_SESSION['site_invite_email_body'])) echo $_SESSION['site_invite_email_body']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <!-- Email From Name -->
                        <div class="form-group">
                            <div class="col">
                                <label for="email_from_name" class="col-form-label"> Invite Email From Name </label>
                                <input type="text" class="form-control" id="email_from_name" name="email_from_name" value="<?php echo $_SESSION["site_email_from_name"] ?>">
                            </div>
                        </div>

                        <!-- Email From Address -->
                        <div class="form-group">
                            <div class="col"> 
                                <label for="email_from_address" class="col-form-label"> Invite Email From Address </label>
                                <input type="text" class="form-control" id="email_from_address" name="email_from_address" value="<?php echo $_SESSION["site_email_from_address"] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Email Server -->
                        <div class="form-group">
                            <div class="col"> 
                                <label for="email_server" class="col-form-label"> Email Server </label>
                                <input type="text" class="form-control" id="email_server" name="email_server" value="<?php echo $_SESSION["site_email_server"] ?>">
                            </div>
                        </div>

                        <!-- Email Server Port -->
                        <div class="form-group">
                            <div class="col"> 
                                <label for="email_server_port" class="col-form-label"> Email Server Port </label>
                                <input type="text" class="form-control" id="email_server_port" name="email_server_port" value="<?php echo $_SESSION["site_email_server_port"] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Email Server Account -->
                        <div class="form-group">
                            <div class="col">
                                <label for="email_server_account" class="col-form-label"> Email Server Account </label>
                                <input type="text" class="form-control" id="email_server_account" name="email_server_account" value="<?php echo $_SESSION["site_email_server_account"] ?>">
                            </div>
                        </div>

                        <!-- Email Server Password -->
                        <div class="form-group">
                            <div class="col">
                                <label for="email_server_password" class="col-form-label"> Email Server Password </label>
                                <input type="text" class="form-control" id="email_server_password" name="email_server_password" value="<?php echo $_SESSION["site_email_server_password"] ?>">
                            </div>
                        </div>
                    </div>

                </div><!-- END Email Tab -->
            </div>

            <button type="submit" class="btn btn-primary btn-block" name="save_button">Save</button>
            <a href="../index.php" class="text-secondary d-block mt-2 text-center">Cancel</a>
        </form>
    </section> <!-- END id user_settings -->
</main>
{footer}
<?php ob_end_flush(); ?>