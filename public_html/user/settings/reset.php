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



if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
}

if (isset($_POST["change_pwd"])) {

    $pwd = trim($_POST["pwd"]);
    $confirm_pwd = trim($_POST["confirm_pwd"]);

    // At least one of these is empty: Password cannot be empty
    if (empty($pwd) || empty($confirm_pwd)) {
        header("Location: ./reset.php?m=7&s=warning");
        exit;
    } else {

        if ($pwd != $confirm_pwd) {
            header("Location: ./reset.php?m=5&s=warning");
            exit;

        } else {
            $pwd_peppered = hash_hmac($hash_algorithm, $pwd, $pepper);
            if (password_verify($pwd_peppered, $_SESSION["password"])) {
                // can't use the old password
                header("Location: ./reset.php?m=4&s=warning");
                exit;
            } else {
                // Update the password
                $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $pwd, $pepper), PASSWORD_BCRYPT);
                $sql = "UPDATE user SET password=:password WHERE email=:email";
                if (!$pdo->prepare($sql)->execute(['password' => $hashed_pwd, 'email' => $_SESSION["email"]])) {
                    // server error: hopefully this edge case will never happen
                    header("Location: ./reset.php?m=6&s=warning");
                    exit;
                } else {
                    // Redirect back to login with a success message and email inside the email input
                    header("Location: /login/?m=3&s=success&email=" . $_SESSION["email"]);
                    exit;
                }

            }

        }

    }

}

?>
{header}
{main_nav}
	<main role="main" id="admin_site_settings">
		<h1 class="mb-5 sticky-top">Change Password</h1>
    	<section>
			<form class="mt-5" method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
				<small id="passwordHelpBlock" class="form-text text-muted mb-4">
                    Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
                </small>
	            <div class="form-group col-md-6">
	                <label for="pwd" cclass="col-form-label">New Password</label>
	                <input type="password" class="form-control" id="pwd" name="pwd">
				</div>
	            <div class="form-group col-md-6">
	                <label for="confirm_pwd" class="col-form-label">Confirm Password</label>
	                <input type="password" class="form-control" id="confirm_pwd" name="confirm_pwd">
	            </div>
				<button type="submit" class="btn btn-primary btn-block" name="change_pwd">Change Password</button>
	            <a href="/user/" class="text-secondary d-block mt-2 text-center">Cancel</a>
			</form>
		</section>
    </main>
{footer}
<?php ob_end_flush(); ?>
