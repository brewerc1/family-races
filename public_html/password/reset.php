<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

if (!isset($_GET["email"]) && !isset($_GET["code"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

$email = $_GET["email"];
$code = $_GET["code"];

$query = "SELECT email FROM user WHERE pw_reset_code = :pw_reset_code";

$invite_code = $pdo->prepare($query);
$invite_code->execute(['pw_reset_code' => $code]);

if ($invite_code->rowCount() != 1) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}


// Notification System
$notification = "";
if (isset($_GET["message"]))
    $notification = $_GET["message"];


// Change password script
if (isset($_POST["change_pwd"])) {
    $pwd = $_POST["pwd"];
    $confirm_pwd = $_POST["confirm_pwd"];

    if (empty($pwd) || empty($confirm_pwd) || (empty($pwd) && empty($confirm_pwd))) {
        header("Location: /password/reset.php?email=" . $email . "&code=" . $code . "&message=Please enter password");
    } else {
        if ($pwd != $confirm_pwd) {
            header("Location: /password/reset.php?email=" . $email . "&code=" . $code . "&message=Password did not match");
        } else {

            $query = "SELECT * FROM user WHERE email = :email";
            $user = $pdo->prepare($query);
            $user->execute(['email' => $email]);
            $row = $user->fetch();

            if ($user->rowCount() != 1) {
                // server error: hopefully this edge case will never happen
                header("Location: /password/reset.php?email=" . $email . "&code=" . $code . "&message=Server Error: try again");
            } else {
                $pwd_peppered = hash_hmac($hash_algorithm, $pwd, $pepper);
                if (password_verify($pwd_peppered, $row["password"])) {
                    // can't use the old password
                    header("Location: /password/reset.php?email=" . $email . "&code=" . $code . "&message=Can't use old password");
                } else {

                    $hashed_pwd = password_hash(hash_hmac($hash_algorithm, $pwd, $pepper), PASSWORD_BCRYPT);
                    $sql = "UPDATE user SET password=:password WHERE email=:email";
                    if (!$pdo->prepare($sql)->execute(['password' => $hashed_pwd, 'email' => $email])) {
                        // server error: hopefully this edge case will never happen
                        header("Location: /password/reset.php?email=" . $email . "&code=" . $code . "&message=Server error: try again");
                    } else {
                        // start session and redirect user to welcome page?? or redirect user to login page????
                        ob_start();
                        session_start();

                        // Session variables (13)
                        $_SESSION["id"] = $row["id"];
                        $_SESSION["first_name"] = $row["first_name"];
                        $_SESSION["last_name"] = $row["last_name"];
                        $_SESSION["email"] = $row["email"];
                        $_SESSION["create_time"] = $row["create_time"];
                        $_SESSION["update_time"] = $row["update_time"];
                        $_SESSION["city"] = $row["city"];
                        $_SESSION["state"] = $row["state"];
                        $_SESSION["motto"] = $row["motto"];
                        $_SESSION["photo"] = $row["photo"];
                        $_SESSION["sound_fx"] = $row["sound_fx"] == 1; //bool
                        $_SESSION["voiceovers"] = $row["voiceovers"] == 1; //bool
                        $_SESSION["admin"] = $row["admin"] == 1; //bool

                        // Redirect to welcome page
                        header("Location: ../login/welcome/");


                    }

                }
            }

        }
    }

}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>New Password</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<main role="main">

        <form method="POST" action=<?php $_SERVER["PHP_SELF"] ?>>
            <input type="password" name="pwd" placeholder="New Password">
            <input type="password" name="confirm_pwd" placeholder="Confirm Password">
            <!--- Notification System : HTML tag may change-->
            <span>
                <?php
                    echo $notification;
                ?>
            </span>
            <input type="submit" name="change_pwd" value="Change Password" >
        </form>

</main>
<?php
include '../template/footer.php';
?>