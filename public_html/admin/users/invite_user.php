<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

if (isset($_POST["invite"])) {
    $email = $_POST["email"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ./?message=Incorrect Email");
    } else {
        $query = "SELECT email FROM user WHERE email = :email";
        $emails = $pdo->prepare($query);
        $emails->execute(['email' => $email]);

        if ($emails->rowCount() != 0) {
            header("Location: ./?message=User already invited");
        } else {

            $query = "SELECT invite_email_subject, invite_email_body, email_server, email_server_port, email_server_account, email_server_password, email_from_name, email_from_address FROM site_settings";
            $email_arguments = $pdo->prepare($query);
            $email_arguments->execute();

            if ($email_arguments->rowCount() == 0) {
                header("Location: ./?message=DB is empty");
            } else {

                try {
                    $unique_code = generateCode();
                } catch (Exception $e) {
                    header("Location: ./?message=Fails to generate Code");
                    exit; // a bug?
                }

                // write to the db
                $default_password = "123456";
                $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $default_password, $pepper), PASSWORD_BCRYPT);
                $sql = "INSERT INTO user (email, invite_code, password) VALUES (?,?,?)";
                if (!$pdo->prepare($sql)->execute([$email, $unique_code, $hashed_pwd])) {
                    header("Location: ./?message=Couldn't write to DB");

                } else {
                    $row = $email_arguments->fetch();
                    // send invite
                    $invite_email_body = "<p>" . $row["invite_email_body"] . "<br /> <ul> <li>Code: ". $unique_code ." </li> <li>Default Password: 123456</li> <li>Link: <a href=\"http://localhost/onboarding/?email=$email&code=$unique_code\">familyrace</a></li> </ul> </p>";

                    $is_sent = sendEmail($row["email_server"], $row["email_server_account"],
                        $row["email_server_password"], $row["email_server_port"], $row["email_from_name"],
                        $row["email_from_address"], $row["invite_email_subject"], $invite_email_body, $email);

                    if (!$is_sent) {
                        header("Location: ./?message=Invite not sent");
                    } else {
                        header("Location: ./?message=Invite sent");
                    }

                }

            }
        }

    }

}

?>