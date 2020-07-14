<?php

// Get the configuration array from the config file located outside the document root.
require_once( $_SERVER['DOCUMENT_ROOT'] . '/../system/config.php');

// Those information are used to hash password.
$hash_algorithm = "sha256";
$pepper = "clisvFdxMd2020";

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
    return <<< HERE
    <button class="btn btn-link btn-sm" id="debug_button" type="button" data-toggle="collapse" data-target="#debug" aria-expanded="false" aria-controls="debug">debug</button>
    <div class="collapse" id="debug">
        <div class="card card-body" id="debug_card">
        <nav>
            <div class="nav nav-pills nav-justified" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-session-tab" data-toggle="tab" href="#nav-session" role="tab" aria-controls="nav-session" aria-selected="true">\$_SESSION</a>
                <a class="nav-item nav-link" id="nav-debug-tab" data-toggle="tab" href="#nav-debug" role="tab" aria-controls="nav-debug" aria-selected="false">Debug</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-session" role="tabpanel" aria-labelledby="nav-session-tab">$session_content;</div>
            <div class="tab-pane fade" id="nav-debug" role="tabpanel" aria-labelledby="nav-debug-tab">$content</div>
        </div>
HERE;
}
