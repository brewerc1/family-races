<?php
/**
 * Page to Change User Password
 *
 * Allows user to change password outside the login/forgot password flow.
 */
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "Change Password";

// include the menu javascript for the template
$javascript = '';



if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

if (isset($_POST["change_pwd"])) {

    $pwd = trim($_POST["pwd"]);
    $confirm_pwd = trim($_POST["confirm_pwd"]);

    // At least one of these is empty: Password cannot be empty
    if (empty($pwd) || empty($confirm_pwd)) {
        header("Location: ./reset.php?m=7&s=warning");
        // Make sure the rest of code is not gonna be executed
        exit;
    } else {

        if ($pwd != $confirm_pwd) {
            header("Location: ./reset.php?m=5&s=warning");
            // Make sure the rest of code is not gonna be executed
            exit;

        } else {
            $pwd_peppered = hash_hmac($hash_algorithm, $pwd, $pepper);
            if (password_verify($pwd_peppered, $_SESSION["password"])) {
                // can't use the old password
                header("Location: ./reset.php?message=4&s=warning");
                // Make sure the rest of code is not gonna be executed
                exit;
            } else {
                // Update the password
                $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $pwd, $pepper), PASSWORD_BCRYPT);
                $sql = "UPDATE user SET password=:password WHERE email=:email";
                if (!$pdo->prepare($sql)->execute(['password' => $hashed_pwd, 'email' => $_SESSION["email"]])) {
                    // server error: hopefully this edge case will never happen
                    header("Location: ./reset.php?m=6&s=warning");
                    // Make sure the rest of code is not gonna be executed
                    exit;
                } else {
                    // Redirect back to login with a success message and email inside the email input
                    header("Location: /login/?m=3&s=success&email=" . $_SESSION["email"]);
                    // Make sure the rest of code is not gonna be executed
                    exit;
                }

            }

        }

    }

}

?>
{header}
{main_nav}
    <main role="main">
        <div class="container">
            <form class="mt-5" method="POST" action=<?php $_SERVER["PHP_SELF"] ?> >
                <div class="form-group">
                    <label for="pwd" class="sr-only">New Password</label>
                    <input type="password" name="pwd" placeholder="New Password">
                </div>
                <div class="form-group">
                    <label for="confirm_pwd" class="sr-only">Confirm Password</label>
                    <input type="password" name="confirm_pwd" placeholder="Confirm Password">
                </div>
                <!-- <button type="submit" class="btn btn-primary btn-block" name="change_pwd">Change Password</button>-->
                <input type="submit" name="change_pwd" value="Change Password" class="btn btn-primary btn-block">
                <a href="/user/" class="text-secondary d-block mt-2 text-center">Cancel</a>
            </form>
        </div>
    </main>
{footer}
<?php ob_end_flush(); ?>