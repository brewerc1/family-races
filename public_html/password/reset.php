<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
ob_start('template');


if (!isset($_GET["email"]) && !isset($_GET["code"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$email = trim($_GET["email"]);
$code = trim($_GET["code"]);

// If the email passed in GET["email"] is not a valid email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}


if (isset($_POST["change_pwd"])) {

    $query = "SELECT password FROM user WHERE email = :email AND pw_reset_code = :pw_reset_code";

    $user = $pdo->prepare($query);
    if (!$user->execute(['email' => $email, 'pw_reset_code' => $code])) {
        header("Location: /password/reset.php?m=6&s=warning&email=" . $email ."&code=" . $code);
        // Make sure the rest of code is not gonna be executed
        exit;
    } else {

        if ($user->rowCount() != 1) {
            header("Location: /password/reset.php?m=6&s=warning&email=" . $email ."&code=" . $code);
            // Make sure the rest of code is not gonna be executed
            exit;
        } else {
            $row = $user->fetch();
            //var_dump($row);
            $pwd = trim($_POST["pwd"]);
            $confirm_pwd = trim($_POST["confirm_pwd"]);

            // At least one of these is empty: Password cannot be empty
            if (empty($pwd) || empty($confirm_pwd)) {
                header("Location: /password/reset.php?m=7&s=warning&email=" . $email ."&code=" . $code);
                // Make sure the rest of code is not gonna be executed
                exit;

            } else {

                if ($pwd != $confirm_pwd) {
                    header("Location: /password/reset.php?m=5&s=warning&email=" . $email ."&code=" . $code);
                    // Make sure the rest of code is not gonna be executed
                    exit;
                } else {
                    // Check if old password
                    $pwd_peppered = hash_hmac($hash_algorithm, $pwd, $pepper);
                    if (password_verify($pwd_peppered, $row["password"])) {
                        // can't use the old password
                        header("Location: /password/reset.php?m=4&s=warning&email=" . $email ."&code=" . $code);
                        // Make sure the rest of code is not gonna be executed
                        exit;
                    } else {
                        // Update the password
                        $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $pwd, $pepper), PASSWORD_BCRYPT);
                        $sql = "UPDATE user SET password=:password, pw_reset_code= :pw_reset_code WHERE email=:email";
                        if (!$pdo->prepare($sql)->execute(['password' => $hashed_pwd, 'pw_reset_code' => NULL, 'email' => $email])) {
                            // server error: hopefully this edge case will never happen
                            header("Location: /password/reset.php?m=6&s=warning&email=" . $email ."&code=" . $code);
                            // Make sure the rest of code is not gonna be executed
                            exit;
                        } else {
                            // Redirect back to login with a success message and email inside the email input
                            header("Location: /login/?m=3&s=success&email=" . $email);
                            // Make sure the rest of code is not gonna be executed
                            exit;
                        }
                    }

                }

            }

        }


    }

}


//header("Location: ./?message=3&alt=2");
// set the page title for the template
$page_title = "Create New Password";

// include the menu javascript for the template
$javascript = "";

// Notification System
$messages = array(
    1 => "Something went wrong",
    2 => "Server Error: Try again",
    3 => "Password cannot be empty",
    4 => "Passwords did not match",
    5 => "Can't use old password"
);

$alerts = array(
    1 => "success",
    2 => "warning"
);

?>
{header}
<main role="main">

        <form method="POST" action=<?php $_SERVER["PHP_SELF"] ?>>
            <input type="password" name="pwd" placeholder="New Password">
            <input type="password" name="confirm_pwd" placeholder="Confirm Password">
            <input type="submit" name="change_pwd" value="Change Password" >
        </form>

</main>
{footer}
<?php ob_end_flush(); ?>