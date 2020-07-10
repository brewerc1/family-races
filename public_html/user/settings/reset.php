<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

$page_title = "Change Password";
$javascript = '';

// Authentication System
ob_start('template');
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /login/");

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");

}

if (isset($_POST["change_pwd"])) {

    $pwd = trim($_POST["pwd"]);
    $confirm_pwd = trim($_POST["confirm_pwd"]);

    // At least one of these is empty: Password cannot be empty
    if (empty($pwd) || empty($confirm_pwd)) {
        header("Location: ./reset.php?message=2&alt=2");
    } else {

        if ($pwd != $confirm_pwd) {
            header("Location: ./reset.php?message=3&alt=2");

        } else {
            $pwd_peppered = hash_hmac($hash_algorithm, $pwd, $pepper);
            if (password_verify($pwd_peppered, $_SESSION["password"])) {
                // can't use the old password
                header("Location: ./reset.php?message=4&alt=2");
            } else {
                // Update the password
                $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $pwd, $pepper), PASSWORD_BCRYPT);
                $sql = "UPDATE user SET password=:password WHERE email=:email";
                if (!$pdo->prepare($sql)->execute(['password' => $hashed_pwd, 'email' => $_SESSION["email"]])) {
                    // server error: hopefully this edge case will never happen
                    header("Location: ./reset.php?message=1&alt=2");
                } else {
                    // Redirect back to login with a success message and email inside the email input
                    header("Location: /login/?message=3&alt=1&email=" . $_SESSION["email"]);
                }

            }

        }

    }

}

// Notification System
$messages = array(
    1 => "Server Error: Try again",
    2 => "Password cannot be empty",
    3 => "Passwords did not match",
    4 => "Can't use old password"
);

$alerts = array(
    1 => "success",
    2 => "warning"
);

$notification = "";
$alert = "";
if (isset($_GET["message"]) && isset($_GET["alt"])) {
    $not = trim($_GET["message"]);
    $al = trim($_GET["alt"]);

    if ($not == 1 || $not == 2 || $not == 3 || $not == 4 )
        $notification = $messages[$not];
    if ($al == 1 || $al == 2 )
        $alert = $alert_style[$alerts[$al]];

}
?>
{header}
{main_nav}
    <main role="main">
        <form method="POST" action=<?php $_SERVER["PHP_SELF"] ?>>
            <input type="password" name="pwd" placeholder="New Password">
            <input type="password" name="confirm_pwd" placeholder="Confirm Password">
            <!--- Notification System : HTML tag may change-->
            <?php if((isset($notification) && $notification != '') && (isset($_GET["alt"]) && $alert != '')){?>
                <div class="alert <?php echo $alert ?> alert-dismissible fade show" role="alert">
                    <?php echo $notification; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
            <input type="submit" name="change_pwd" value="Change Password" >
        </form>
    </main>
{footer}
<?php ob_end_flush(); ?>