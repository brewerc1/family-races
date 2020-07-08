<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

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
            $query = "SELECT invite_email_subject, invite_email_body, email_server, email_server_port, email_server_account, email_server_password, email_from_name, email_from_address FROM site_settings";
            $email_arguments = $pdo->prepare($query);
            $email_arguments->execute();
            $row = $email_arguments->fetch();

            if ($email_arguments->rowCount() == 0) {
                header("Location: ./?message=3&alt=2");
            } else {

                if (empty($row["invite_email_subject"]) || empty($row["invite_email_body"]) ||
                    empty($row["email_server"]) || empty($row["email_server_port"]) ||
                    empty($row["email_server_account"]) || empty($row["email_server_password"]) ||
                    empty($row["email_from_name"]) || empty($row["email_from_address"])) {

                    header("Location: ./?message=3&alt=2");

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
                        //$row = $email_arguments->fetch();
                        // send invite
                        $host = $_SERVER['SERVER_NAME'];
                        $invite_email_body = "<p>" . $row["invite_email_body"] . " <ul> <li>Code: $code</li> <li><a href=\"http://$host/onboarding/?email=$email&code=$unique_code\">family race</a></li> </ul> </p>";

                        if (!sendEmail($row["email_server"], $row["email_server_account"],
                            $row["email_server_password"], $row["email_server_port"], $row["email_from_name"],
                            $row["email_from_address"], $row["invite_email_subject"], $invite_email_body, $email)) {

                            header("Location: ./?message=6&alt=2");

                        } else {
                            header("Location: ./?message=7&alt=1");
                        }

                    }
                }

            }


        }

    }

}

?>