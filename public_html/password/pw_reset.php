<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');


if (isset($_POST["reset_password"])) {
    $email = $_POST["email"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: /password/?message=Invalid Email");
    } else {

        $query = "SELECT * FROM user WHERE email = :email";
        $user = $pdo->prepare($query);
        $user->execute(['email' => $email]);

        if ($user->rowCount() != 1) {
            header("Location: /password/?message=Email Address not associated to an account");
        } else {

            $first_name = $user->fetch()["first_name"];

            $query = "SELECT email_server, email_server_port, email_server_account, email_server_password, email_from_name, email_from_address FROM site_settings";
            $email_arguments = $pdo->prepare($query);
            $email_arguments->execute();

            if ($email_arguments->rowCount() == 0) {
                header("Location: ./?message=Server Error: Try again");
            } else {
                // reset code: throws an exception
                try {
                    $reset_pw_code = generateCode();
                } catch (Exception $e) {
                    header("Location: /password/?message=Server Error: Try again");
                }

                // Write to DB: update pw_reset_code
                $sql = "UPDATE user SET pw_reset_code=:pw_reset_code WHERE email=:email";
                if (!$pdo->prepare($sql)->execute(['pw_reset_code' => $reset_pw_code, 'email' => $email])) {
                    header("Location: /password/?message=Server Error: Try again");
                } else {

                    $row = $email_arguments->fetch();
                    // send reset pw code
                    $pw_reset_email_subject = "Reset Your Password";
                    $pw_reset_email_body = "<h3>Hi " . $first_name . ",</h3> <p>We've received a request to reset your password. If you did not make this request, 
click <a href=\"http://localhost/password/cancel.php?email=$email&code=$reset_pw_code\">here</a> to cancel the request. Otherwise, click this link to reset your password</p> <p><a href=\"http://localhost/password/reset.php?email=$email&code=$reset_pw_code\">Click here to reset your password</a></p> <p>Thanks, <br /> The Family Race!</p>";

                    $is_sent = sendEmail($row["email_server"], $row["email_server_account"],
                        $row["email_server_password"], $row["email_server_port"], $row["email_from_name"],
                        $row["email_from_address"], $pw_reset_email_subject, $pw_reset_email_body, $email);

                    if (!$is_sent) {
                        header("Location: /password/?message=Server Error: Try again");
                    } else {
                        header("Location: /password/?message=Check your email");
                    }
                }

            }


        }

    }

}
