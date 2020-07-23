<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
ob_start('template');


// set the page title for the template
$page_title = "Forgot Password";

// include the menu javascript for the template
$javascript = "";

?>
{header}
<main role="main">
    <section>
        <h1>Recover Your Password</h1>
        <p>
            To reset your password. enter your account email address. An email will be sent with a reset code.
        </p>
    </section>

    <section>
        <form action="./pw_reset.php" method="POST">
            <input type="email" name="email" placeholder="Email">
            <input type="submit" name="reset_password" value="Reset Password">
        </form>
        <p>
            <a href="http://localhost/login">Return to Log In</a>
        </p>
    </section>
</main>
{footer}
<?php ob_end_flush(); ?>