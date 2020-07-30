<?php

// Get the configuration array from the config file located outside the document root.
require_once( $_SERVER['DOCUMENT_ROOT'] . '/../system/config.php');

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

// Code for sending out emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
        $mail->setFrom($email_from_address, $email_from_name);
        $mail->addAddress($recipient_email);

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
    <button class="btn btn-link btn-sm" id="debug_button" type="button" data-toggle="collapse" data-target="#debug" aria-expanded="false" aria-controls="debug">debug</button>
    <div class="collapse" id="debug">
        <div class="card card-body" id="debug_card">
            <nav>
                <div class="nav nav-pills nav-justified" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-session-tab" data-toggle="tab" href="#nav-session" role="tab" aria-controls="nav-session" aria-selected="true">\$_SESSION</a>
                    <a class="nav-item nav-link" id="nav-debug-tab" data-toggle="tab" href="#nav-debug" role="tab" aria-controls="nav-debug" aria-selected="false">My Debug</a>
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
    0 => "An email has been sent",
    1 => "Invalid Credentials",
    2 => "Email or Password cannot be empty",
    3 => "Password has been changed. Please log in again.",
    4 => "Can't use the old password",
    5 => "Passwords did not match",
    6 => "Server Error: Try again",
    7 => "Password cannot be empty",
    8 => "Invite not sent",
    9 => "Invite sent",
    10 => "Invalid Email",
    11 => "User already invited",
    12 => "Need to have a selection for horse & place!",
    13 => "Bet Placed!",
    14 => "Bet Updated!",
    15 => "Settings Updated!",
    16 => "User Deactivated",
    17 => "User Activated"
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
        'horse-4811946_1920.jpg' => 'Karuvadgraphy</a>"',
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