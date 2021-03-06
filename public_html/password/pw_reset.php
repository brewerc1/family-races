<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

if (isset($_POST["reset_password"])) {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        header("Location: /password/?m=2&s=warning");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: /password/?m=10&s=warning");
        exit;
    } else {

        $query = "SELECT * FROM user WHERE email = :email";
        $user = $pdo->prepare($query);
        $user->execute(['email' => $email]);

        if ($user->rowCount() != 1) {
            header("Location: /password/?m=0&s=success&email=" . $email);
            exit;
        } else {

            $first_name = $user->fetch()["first_name"];

            $query = "SELECT email_server, email_server_port, email_server_account, email_server_password, email_from_name, email_from_address FROM site_settings";
            $email_arguments = $pdo->prepare($query);
            $email_arguments->execute();

            if ($email_arguments->rowCount() == 0) {
                header("Location: /password/?m=28&s=warning");
                exit;
            } else {

                // reset code: throws an exception
                try {
                    $reset_pw_code = generateCode();
                } catch (Exception $e) {
                    header("Location: /password/?m=29&s=warning");
                    exit;
                }

                // Write to DB: update pw_reset_code
                $sql = "UPDATE user SET pw_reset_code=:pw_reset_code WHERE email=:email";
                if (!$pdo->prepare($sql)->execute(['pw_reset_code' => $reset_pw_code, 'email' => $email])) {
                    header("Location: /password/?m=30&s=warning");
                    exit;
                } else {

                    $row = $email_arguments->fetch();
                    // send reset pw code
                    $pw_reset_email_subject = "Reset Your Password";
                    $host = $_SERVER["SERVER_NAME"];
                    $pw_reset_email_body = "<h3>Hi " . $first_name . ",</h3> <p>We've received a request to reset your password. If you did not make this request, please ignore this. Otherwise, click this link to reset your password:</p> <p><a href=\"http://$host/password/reset.php?email=$email&code=$reset_pw_code\">Click here to reset your password</a></p> <p>Thanks, <br /> {$_SESSION['site_name']}</p>";

                    $is_sent = sendEmail($row["email_server"], $row["email_server_account"],
                        $row["email_server_password"], $row["email_server_port"], $row["email_from_name"],
                        $row["email_from_address"], $pw_reset_email_subject, $pw_reset_email_body, $email);


                    if (!$is_sent) {
                        header("Location: /password/?m=31&s=warning");
                        exit;
                    } else {
                        header("Location: /password/?m=34&s=success&email=" . $email);
                        exit;
                    }
                }

            }
        }
    }
}
