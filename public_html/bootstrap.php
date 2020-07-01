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
 *
 * @author makungaj1
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
 *
 *  This function returns a unique code like Lx7!3469187
 *
 *  The code starts with an uppercase letter followed by a lowercase letter
 *  followed by a digit. That digit specifies the number of digit at the end of the code
 *
 *  Hw8!13234720
 *
 *  Hw8! : 8 indicates 8 digits at the end of the code '13234720'
 *
 * @author makungaj1
 */
function generateCode()
{

    $second = date("s");

    $year_month_date = date("Ymd");

    $upper = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z');

    $lower = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
        'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

    $s_char = array('!', '~', '#', '@', '$', '%', '&', '*');


    $end_digits = rand($second, $year_month_date);

    $count_digits = strlen((string)$end_digits);

    return $upper[array_rand($upper)] . $lower[array_rand($lower)] . $count_digits .
        $s_char[array_rand($s_char)] . $end_digits;

}

?>
