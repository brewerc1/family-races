<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
ob_start('template');
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0) {

    if (!isset($_GET["email"]) && !isset($_GET["code"])) {
        header("HTTP/1.1 401 Unauthorized");
        // An error page
        //header("Location: error401.php");
        exit;
    }
}

$email = isset($_GET["email"]) ? trim($_GET["email"]) : $_SESSION["email"];
$code = isset($_GET["code"]) ? trim($_GET["code"]) : NULL;

// If the email passed in GET["email"] is not a valid email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

if (isset($_POST["change_pwd"])) {

    $logged_in_user_query = "SELECT password FROM user WHERE id = :id";
    $not_logged_in_user_query = "SELECT password FROM user WHERE email = :email AND pw_reset_code = :pw_reset_code";
    $data = is_null($code) ? array('id' => $_SESSION["id"]) : array('email' => $email, 'pw_reset_code' => $code);
    $query = is_null($code) ? $logged_in_user_query : $not_logged_in_user_query;

    $user = $pdo->prepare($query);
    if (!$user->execute($data)) {
        header("Location: ./?message=2&alt=2");
    } else {

        if ($user->rowCount() != 1) {
            header("Location: ./?message=1&alt=2");
        } else {
            $row = $user->fetch();
            var_dump($row);
            //SELECT password FROM user WHERE email = :email AND pw_reset_code = :pw_reset_code
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
    3 => "Server Error: Try again",
    4 => "Check your email"
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