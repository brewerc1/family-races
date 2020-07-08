<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start();
// start a session
session_start();

if (isset($_POST["invite"])) {
    $email = trim($_POST["email"]);

    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        header("Location: ./?message=1&alt=2");
    } else {
        $query = "SELECT email FROM user WHERE email = :email";
        $emails = $pdo->prepare($query);
        $emails->execute(['email' => $email]);

        if ($emails->rowCount() != 0) {
            header("Location: ./?message=2&alt=2");
        } else {
            try {
                $unique_code = generateCode();
            } catch (Exception $e) {
                header("Location: ./?message=4&alt=2");
                exit; //
            }
            // write to the db
            $sql = "INSERT INTO user (email, invite_code) VALUES (?,?)";
            if (!$pdo->prepare($sql)->execute([$email, $unique_code])) {
                header("Location: ./?message=5&alt=2");
            } else {
                // send invite
                $host = $_SERVER['SERVER_NAME'];
                $invite_email_body = "<p>" . $_SESSION["site_invite_email_body"] . " <ul> <li>Code: $code</li> <li><a href=\"http://$host/onboarding/?email=$email&code=$unique_code\">family race</a></li> </ul> </p>";

                if (!sendEmail($_SESSION["site_email_server"], $_SESSION["site_email_server_account"],
                    $_SESSION["site_email_server_password"], $_SESSION["site_email_server_port"],
                    $_SESSION["site_email_from_name"], $_SESSION["site_email_from_address"],
                    $_SESSION["site_invite_email_subject"], $invite_email_body, $email)) {

                    header("Location: ./?message=6&alt=2");

                } else {
                    header("Location: ./?message=7&alt=1");
                }

            }


        }

    }

}

?>