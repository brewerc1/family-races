<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
ob_start('template');


// set the page title for the template
$page_title = "Forgot Password";

// include the menu javascript for the template
$javascript = "";


// Notification System
$msg = "";
if (isset($_GET["email"])) {
    $email = trim($_GET["email"]);
    $msg = filter_var($email, FILTER_VALIDATE_EMAIL) ? "An email has been sent to " . $email : "Email sent" ;
}
$messages = array(
    1 => "Invalid Email",
    2 => "Server Error: Try again",
    3 => $msg
);

$alerts = array(
    1 => "success",
    2 => "warning"
);

$notification = "";
$alert = "";
if (isset($_GET["message"]) && isset($_GET["alt"])) {
    $not = $_GET["message"];
    $al = $_GET["alt"];

    if ($not == 1 || $not == 2 || $not == 3 || $not == 4 )
        $notification = $messages[$not];
    if ($al == 1 || $al == 2 )
        $alert = $alert_style[$alerts[$al]];

}
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
            <!--- Notification System : HTML tag may change-->
            <?php if((isset($notification) && $notification != '') && (isset($_GET["alt"]) && $alert != '')){?>
                <div class="alert <?php echo $alert ?> alert-dismissible fade show" role="alert">
                    <?php echo $notification; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
            <input type="submit" name="reset_password" value="Reset Password">
        </form>
        <p>
            <a href="http://localhost/login">Return to Log In</a>
        </p>
    </section>
</main>
{footer}
<?php ob_end_flush(); ?>