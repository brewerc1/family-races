<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

// set the page title for the template
$page_title = "Forgot Password";

// include the menu javascript for the template
$javascript = "";

?>
{header}
<main role="main" id="password_reset_page">
	<h1 class="mb-5 sticky-top">Reset Your Password</h1>
    <section id="reset_password"> 
        <form action="./pw_reset.php" method="POST">
            <div class="form-group col-md-6">
                <label class="sr-only" for="email">Email Address</label>
                <input type="email" class="form-control" aria-describedby="email_help" name="email" id="email" placeholder="example@email.com">
                <small class="form-text text-muted" id="email_help" name="email_help">
                    To reset your password, please enter your account email address. An email will be sent with a reset code.
                </small>
            
                <input type="submit" class="btn btn-primary" name="reset_password" value="Reset Password">
                <a href="/login" class="text-secondary d-block mt-2">Return to Log In</a>
            </div>
        </form>
    </section>
</main>
{footer}
<?php ob_end_flush(); ?>