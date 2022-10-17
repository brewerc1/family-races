<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

if (!isset($_GET['email']) || !isset($_GET['code'])) {
	header("Location: /login/index.php");
    exit;
}

$email = trim($_GET["email"]);
$code = trim($_GET["code"]);

// If the email passed in GET["email"] is not a valid email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	header("Location: /password/reset.php?m=10&s=warning");
    exit;
}

if (isset($_POST["change_pwd"])) {

    $query = "SELECT password FROM user WHERE email = :email AND pw_reset_code = :pw_reset_code";
	$user = $pdo->prepare($query);
	
    if (!$user->execute(['email' => $email, 'pw_reset_code' => $code])) {
        header("Location: /password/reset.php?m=27&s=warning&email=" . $email ."&code=" . $code);
        exit;
    } else {
        if ($user->rowCount() != 1) {
            header("Location: /password/reset.php?m=33&s=warning&email=" . $email ."&code=" . $code);
            exit;
        } else {
            $row = $user->fetch();
            $pwd = trim($_POST["pwd"]);
            $confirm_pwd = trim($_POST["confirm_pwd"]);

            // At least one of these is empty: Password cannot be empty
            if (empty($pwd) || empty($confirm_pwd)) {
                header("Location: /password/reset.php?m=7&s=warning&email=" . $email ."&code=" . $code);
                exit;
            } else {
				// Password and Confirm Password must match
                if ($pwd != $confirm_pwd) {
                    header("Location: /password/reset.php?m=5&s=warning&email=" . $email ."&code=" . $code);
                    exit;
                } else {
                    // Check if old password
                    $pwd_peppered = hash_hmac($hash_algorithm, $pwd, $pepper);
                    if (password_verify($pwd_peppered, $row["password"])) {
                        // can't use the old password
                        header("Location: /password/reset.php?m=4&s=warning&email=" . $email ."&code=" . $code);
                        exit;
                    } else {
                        // Update the password
                        $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $pwd, $pepper), PASSWORD_BCRYPT);
                        $sql = "UPDATE user SET password=:password, pw_reset_code= :pw_reset_code WHERE email=:email";
                        if (!$pdo->prepare($sql)->execute(['password' => $hashed_pwd, 'pw_reset_code' => NULL, 'email' => $email])) {
                            // server error: hopefully this edge case will never happen
                            header("Location: /password/reset.php?m=32&s=warning&email=" . $email ."&code=" . $code);
                            exit;
                        } else {
                            // Redirect back to login with a success message and email address inside the email input
                            header("Location: /login/?m=3&s=success&email=" . $email);
                            exit;
                        }
                    }
                }

            }

        }
    }
}

// set the page title for the template
$page_title = "Create New Password";

$debug = debug($_POST);

// include the menu javascript for the template
$javascript = "";

?>
{header}
<main role="main" id="password_reset_page">
    <h1 class="mb-5 sticky-top">Password Reset: <br class="d-md-none">Step 2 of 2</h1>
	<section id="reset_password">
        <form action="<?php $_SERVER["PHP_SELF"];?>" method="POST" class="d-flex justify-content-center align-items-center">
            <div class="form-group col-md-4">
                <small class="form-text text-muted mb-3" id="pwd_help" name="pwd_help">
                    Enter your new password twice, making sure they match.
                </small>
				<div class="form-group">
                	<label class="sr-only" for="pwd">New Password</label>
                	<input type="password" name="pwd" id="pwd" placeholder="New Password" class="form-control" aria-describedby="pwd_help">
				</div>
				<div class="form-group">
					<label class="sr-only" for="confirm_pwd">Confirm Password</label>
					<input type="password" name="confirm_pwd" id="confirm_pwd" placeholder="Confirm Password" class="form-control" aria-describedby="pwd_help">
                </div>
				<input type="submit" class="btn btn-primary btn-block mt-5" name="change_pwd" value="Change Password">
                <a href="/login" class="btn btn-text d-block mt-2 text-center">Return to Log In</a>
            </div>
        </form>
    </section>
</main>
{footer}
<?php ob_end_flush(); ?>