<?php

// Get the configuration array from the config file located outside the document root.
require_once( $_SERVER['DOCUMENT_ROOT'] . '/../system/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


/**
 * @param $host
 * @param $username
 * @param $password
 * @param $to
 * @param $content : may use html5 tags and should have the invite code included.
 * @param string $subject optional
 * @param string $from optional
 * @param int $port optional
 *
 * @return bool
 *
 *
 *  May have to require vendor/autoload.php
 */
function sendEmail($host, $username, $password, $to, $content, $subject="", $from="", $port=587) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $username;
    $mail->Password   = $password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $port;


    try {

        if (empty($from))
            $from = $username;

        //Recipients
        $mail->setFrom($from);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->AltBody = strip_tags($content);

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
