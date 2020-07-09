<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "Login";

// include the menu javascript for the template
$javascript = "";


// Self explanatory
$value = "";
if (!empty($_GET["email"])) {
    $value = $_GET["email"];
}

if (isset($_POST["login"])) {

    $email = $_POST["email"];
    $password = $_POST["pwd"];


    if (empty($email) || empty($password)) {

        // Redirect to login with email, if not empty, inside the placeholder
        header("Location: /login/?login=false&email=" . $email . "&message=Invalid Credentials");

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect to login if invalid email
        header("Location: /login/?login=false&message=Invalid Credentials");

    } else {

        $query = "SELECT * FROM user WHERE email = :email";

        $user = $pdo->prepare($query);
        $user->execute(['email' => $email]);

        if ($user->rowCount() != 1) {
            // Redirect to login if rowcount is not 1
            header("Location: /login/?login=false&email=" . $email . "&message=Invalid Credentials");

        } else {
            $user_row = $user->fetch();
            $pwd = $user_row["password"];

            $pwd_peppered = hash_hmac($hash_algorithm, $password, $pepper);

            if (!password_verify($pwd_peppered, $pwd)) {
                // redirect to login if password don't match
                header("Location: /login/?login=false&email=" . $email . "&message=Invalid Credentials");

            } else {
                // Valid credentials

                // USER: Session variables (13)
                $_SESSION["id"] = $user_row["id"];
                $_SESSION["first_name"] = $user_row["first_name"];
                $_SESSION["last_name"] = $user_row["last_name"];
                $_SESSION["email"] = $user_row["email"];
                $_SESSION["create_time"] = $user_row["create_time"];
                $_SESSION["update_time"] = $user_row["update_time"];
                $_SESSION["city"] = $user_row["city"];
                $_SESSION["state"] = $user_row["state"];
                $_SESSION["motto"] = $user_row["motto"];
                $_SESSION["photo"] = $user_row["photo"];
                $_SESSION["sound_fx"] = $user_row["sound_fx"];
                $_SESSION["voiceovers"] = $user_row["voiceovers"];
                $_SESSION["admin"] = $user_row["admin"];

                // SITE SETTINGS: Session variables
                $query = "SELECT * FROM site_settings";
                $site_setts = $pdo->prepare($query);
                // Question: what to do if line 81 resulted to false
                if ($site_setts->execute() ) {

                    // Question: what to do if line 84 resulted to false
                    if ($site_setts->rowCount() > 0) {
                        $site_row = $site_setts->fetch();
                        $_SESSION["site_sound_fx"] = $site_row["sound_fx"];
                        $_SESSION["site_voiceovers"] = $site_row["voiceovers"];
                        $_SESSION["site_sound_fx"] = $site_row["sound_fx"];
                        $_SESSION["site_terms_enable"] = $site_row["terms_enable"];
                        $_SESSION["site_sound_fx"] = $site_row["sound_fx"];
                        $_SESSION["site_default_horse_count"] = $site_row["default_horse_count"];
                        $_SESSION["site_memorial_race_enable"] = $site_row["memorial_race_enable"];
                        $_SESSION["site_memorial_race_name"] = $site_row["memorial_race_name"];
                        $_SESSION["site_memorial_race_number"] = $site_row["memorial_race_number"];
                        $_SESSION["site_welcome_video_url"] = $site_row["welcome_video_url"];
                        $_SESSION["site_invite_email_subject"] = $site_row["invite_email_subject"];
                        $_SESSION["site_invite_email_body"] = $site_row["invite_email_body"];
                        $_SESSION["site_email_server"] = $site_row["email_server"];
                        $_SESSION["site_email_server_port"] = $site_row["email_server_port"];
                        $_SESSION["site_email_server_account"] = $site_row["email_server_account"];
                        $_SESSION["site_email_server_password"] = $site_row["email_server_password"];
                        $_SESSION["site_email_from_name"] = $site_row["email_from_name"];
                        $_SESSION["site_email_from_address"] = $site_row["email_from_address"];
                    }
                }



                // Redirect to welcome page
                header("Location: /login/welcome/");
            }

        }
    }

}

// Notification System
$notification = "";
if (isset($_GET["message"])) {
    $notification = trim($_GET["message"]);
}

?>
    {header}
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>">
        <input type="email" name="email" placeholder="your@email.com"
               value=<?php echo $value ?>>
        <input type="password" name="pwd" placeholder="password">
        <input type="submit" value="Login" name="login">
    </form>
<?php if(isset($notification) && $notification != ''){?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?php echo $notification; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>
    <div id="forgot_pwd">
        <a href="/password/">Forgot Password</a>
    </div>
    </main>
    {footer}
<?php ob_end_flush(); ?>