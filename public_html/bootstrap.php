<?php

// Get the configuration array from the config file located outside the document root.
require_once( $_SERVER['DOCUMENT_ROOT'] . '/../system/config.php');

// Those information are used to hash password.
// @author makungaj1
$hash_algorithm = "sha256";
$pepper = "clisvFdxMd2020";

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

?>
