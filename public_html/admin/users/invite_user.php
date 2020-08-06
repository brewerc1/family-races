<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start();
// start a session
session_start();

if (isset($_POST["invite"])) {
    $email = trim($_POST["email"]);

    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        header("Location: ./?m=10&s=warning");
        exit;
    } else {
        $query = "SELECT email FROM user WHERE email = :email";
        $emails = $pdo->prepare($query);
        $emails->execute(['email' => $email]);

        if ($emails->rowCount() != 0) {
            header("Location: ./?m=11&s=warning");
            exit;
        } else {
            try {
                $unique_code = generateCode();
            } catch (Exception $e) {
                header("Location: ./?m=6&s=warning");
                exit; //
			}
			$email_encoded = urlencode($email); // properly encode characters for use in a URL
            // write to the db
            $sql = "INSERT INTO user (email, invite_code) VALUES (?,?)";
            if (!$pdo->prepare($sql)->execute([$email, $unique_code])) {
                header("Location: ./?m=6&s=warning");
                exit;
            } else {
                // send invite
                $host = $_SERVER['SERVER_NAME'];
                $invite_email_body = "<p>{$_SESSION["site_invite_email_body"]} <a href=\"http://$host/onboarding/?email=$email_encoded&code=$unique_code\">Click</a> to sign up.</p>\n<p>The invite link and the unique invite code ($unique_code) is specifically tied to the email address entered by the admin ($email). It cannot be used with any other email address. If you want to use a different email address, contact your admin and request a new invite be sent to your different email address.</p>";

                if (!sendEmail($_SESSION["site_email_server"], $_SESSION["site_email_server_account"],
                    $_SESSION["site_email_server_password"], $_SESSION["site_email_server_port"],
                    $_SESSION["site_email_from_name"], $_SESSION["site_email_from_address"],
                    $_SESSION["site_invite_email_subject"], $invite_email_body, $email)) {

                    header("Location: ./?m=8&s=warning");
                    exit;

                } else {
                    header("Location: ./?m=9&s=success");
                    exit;
                }

            }


        }

    }

}

?>