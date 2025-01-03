<?php
// Get the configuration array from the config file located outside the document root.
require_once( $_SERVER['DOCUMENT_ROOT'] . '/../system/config.php');

// Set up default cookie parameters
$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? "{$_SERVER['HTTP_HOST']}" : false;
if(PHP_VERSION_ID < 70300) {
    session_set_cookie_params(
		$config['cookie_lifetime'], 
		'/; samesite='.$config['cookie_samesite'], 
		$domain, 
		$config['cookie_use_https'], 
		$config['cookie_http_only']
	);
} else {
    session_set_cookie_params([
        'lifetime' => $config['cookie_lifetime'],
        'path' => '/',
        'domain' => $domain,
        'secure' => $config['cookie_use_https'],
        'httponly' => $config['cookie_http_only'],
        'samesite' => $config['cookie_samesite']
    ]);
}

// Start a session
session_start();

// get site settings and set session vars
if(empty($_SESSION['site_name'])){
    // SITE SETTINGS: Session variables
	$site_settings_sql = "SELECT * FROM site_settings";
	$site_settings_query = $pdo->prepare($site_settings_sql);
	$site_settings_query->execute();

	if ($site_settings_query->rowCount() > 0) {

		$site_settings_result = $site_settings_query->fetch();
		foreach ($site_settings_result as $site_session_key => $site_session_val)
			$_SESSION["site_" . $site_session_key] = $site_session_val;
	}
}

// Code for page templating
function template($buffer)
{
    global $page_title, $javascript;
    $to_match = array
    (
        '{header}', 
        '{main_nav}', 
        '{footer}'
    );
    $replace_with = array
    (
        require $_SERVER['DOCUMENT_ROOT'] . '/template/header.php', 
        require $_SERVER['DOCUMENT_ROOT'] . '/template/main_nav.php', 
        require $_SERVER['DOCUMENT_ROOT'] . '/template/footer.php'
    );
    return(str_replace($to_match, $replace_with, $buffer));
}

/**
 * @param $pdo
 * @return int
 */
function getCurrentEventId($pdo): int
{
    $query = "SELECT id FROM event ORDER BY id DESC LIMIT 1";
    $current_event = $pdo->prepare($query);
    $current_event->execute();

    if ($current_event->rowCount() > 0) {
        return $current_event->fetch()["id"];
    }

    return 0;
}

// Code for sending out emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Ensure timezone is set for SMTP
date_default_timezone_set('America/Kentucky/Louisville');

require $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/SMTP.php';

/**
 * @param $email_server
 * @param $email_server_account
 * @param $email_server_password
 * @param $email_server_port
 * @param $email_from_name
 * @param $email_from_address
 * @param $invite_email_subject
 * @param $invite_email_body
 * @param $recipient_email
 * @return bool
 */
function sendEmail($email_server, $email_server_account, $email_server_password, $email_server_port,
                   $email_from_name, $email_from_address, $invite_email_subject, $invite_email_body,
                   $recipient_email) {

    $mail = new PHPMailer(true);
    
    if($config['prod_mode'] == false){
		// Set PHPMailer() error reporting for development/debug
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	}

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $email_server;
        $mail->SMTPAuth   = true;
        $mail->Username   = $email_server_account;
        $mail->Password   = $email_server_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $email_server_port;

        //Recipients
        $mail->setFrom($email_server_account, $email_from_name);
        $mail->addAddress($recipient_email);
        $mail->addReplyTo($email_from_address, $email_from_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $invite_email_subject;
        $mail->Body    = $invite_email_body;
        $mail->AltBody = strip_tags($invite_email_body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}


// Password hashing
$hash_algorithm = "sha256";
$pepper = "clisvFdxMd2020";


/**
 * @return string
 * @throws \Exception
 */
function generateCode()
{
    return bin2hex(random_bytes(4));
}


function debug( $content='' ){
    if(!isset($content)) {
        $content='';
    }
    if(isset($_SESSION)) {
        $session_content = "<pre>" . print_r($_SESSION, TRUE) . "</pre>";
    }
    if(is_array($content)){
        $content = "<pre>" . print_r($content, TRUE) . "</pre>";
    }
    return <<< HERE
    <button class="btn btn-link btn-sm animate__animated animate__fadeInBottomLeft animate__delay-1s animate__repeat-1" id="debug_button" type="button" data-toggle="collapse" data-target="#debug" aria-expanded="false" aria-controls="debug">debug</button>
    <div class="collapse" id="debug">
        <div class="card card-body" id="debug_card">
            <nav>
                <div class="nav nav-pills nav-justified" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-session-tab" data-toggle="tab" href="#nav-session" role="tab" aria-controls="nav-session" aria-selected="true">SESSION</a>
                    <a class="nav-item nav-link" id="nav-debug-tab" data-toggle="tab" href="#nav-debug" role="tab" aria-controls="nav-debug" aria-selected="false">PAGE</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-session" role="tabpanel" aria-labelledby="nav-session-tab">$session_content;</div>
                <div class="tab-pane fade" id="nav-debug" role="tabpanel" aria-labelledby="nav-debug-tab">$content</div>
            </div>
        </div>
    </div>
HERE;
}

/* Notification System */

$messages = array(
    0 => "An email has been sent.",
    1 => "Invalid credentials.",
    2 => "Email or password cannot be empty.",
    3 => "Your password has been changed. Please log in again.",
    4 => "Can't re-use the old password.",
    5 => "Passwords do not match.",
    6 => "Server error: Try again.",
    7 => "Password cannot be empty.",
    8 => "Invite not sent.",
    9 => "Invite sent.",
    10 => "Invalid email address.",
    11 => "User already invited.",
    12 => "You must select both horse and place.",
    13 => "Your pick is submitted.",
    14 => "Your pick is updated.",
    15 => "Settings updated.",
    16 => "User deactivated.",
    17 => "User activated.",
    18 => "Invite deleted.",
    19 => "Please fill in a first name.",
    20 => "Please fill in last name.",
    21 => "Please fill in code.",
    22 => "Race closed.",
    23 => "Race cancelled.",
    24 => "Your account is inactive.",
    25 => "Race opened!",
	26 => "Error when placing bet.",
	27 => "Email wasn't sent.",
	28 => "Server error: Can't retrieve email server settings.",
	29 => "Server error: Can't generate random password reset code.",
	30 => "Server error: Can't save random password reset code to database.",
	31 => "Server error: Password reset email wasn't sent.",
	32 => "Server error: Can't save new password to database.",
	33 => "Email address and reset code mismatch during query.",
	34 => "An email has been sent with a password reset link and code.<br>Check your email now."
);

$notification = '';
$alert_style = '';

if (isset($_GET['m']) && isset($_GET['s'])) {
    $message = trim($_GET['m']);
    $style = trim($_GET["s"]);

    if (array_key_exists($message, $messages)) {
        $notification = $messages[$message];
    }
    if (array_key_exists($style, $alert_styles)){
        $alert_style = $alert_styles[$style];
    } else {
        $alert_style = $alert_styles['primary'];
    }
}

/* returns an array, where key 'filename' is the image filename, 
and key 'credit' is the name of the photographer for attribution */
function random_photo(){

    $photos = glob($_SERVER['DOCUMENT_ROOT'] .  '/images/photos/*.jpg');
    
    // Remove path info and just keep filename
    $photos = array_map('basename', $photos);

    // Randomize the array keys, then use the random array key to select one value from the array
    $photo = $photos[array_rand($photos)];

    $photo_credit_array = array(
        'horses-2523301_1920.jpg' => 'Yenni Vance',
        'horse-316960_1280.jpg' => 'No-longer-here',
        'horse-3880448_1920.jpg' => 'Clarence Alford',
        'horse-racing-2714846_1280.jpg' => 'dreamtemp',
        'horse-racing-2714849_1280.jpg' => 'dreamtemp',
        'horse-1911382_1920.jpg' => 'Babil Kulesi',
        'horses-3811270_1920.jpg' => 'Clarence Alford',
        'horse-3433862_1920.jpg' => 'Clarence Alford',
        'horse-3880451_1920.jpg' => 'Clarence Alford',
        'horse-3880449_1920.jpg' => 'Clarence Alford',
        'horse-3880450_1920.jpg' => 'Clarence Alford',
        'horses-3817727_1920.jpg' => 'Clarence Alford',
        'horse-4818530_1920.jpg' => 'Karuvadgraphy',
        'horse-4811946_1920.jpg' => 'Karuvadgraphy"',
        'horse-2815033_1920.jpg' => 'Richard Mcall',
        'horses-2523299_1920.jpg' => 'Yenni Vance',
        'horseracing-5061006_1920.jpg' => 'bianca-stock-photos',
        'race-horse-2629450_1920.jpg' => 'Larry White',
        'horses-2523295_1920.jpg' => 'Yenni Vance',
        'race-4100474_1920.jpg' => 'Ameer shah Mohamed Farook',
        'horse-4100475_1920.jpg' => 'Ameer shah Mohamed Farook'
    );
    
    $photo_array = array(
        'filename' => "/images/photos/$photo"
    );
 
    if(array_key_exists($photo, $photo_credit_array)){
        $photo_array['credit'] = $photo_credit_array[$photo];
    }else{
        $photo_array['credit'] = 'unknown';
    }

    return $photo_array;
}