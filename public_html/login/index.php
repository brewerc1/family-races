<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Turn on output buffering
ob_start('template');

// Start a session
session_start();

// Set the page title for the template
$page_title = "Login";

// Include the menu javascript for the template
$javascript = "";

// Get the email from and put it in the email input
$value = "";
if (!empty($_GET["email"])) {
    $val = trim($_GET["email"]);
    $value = filter_var($val, FILTER_VALIDATE_EMAIL) ? $val : "";
}

if (isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $password = trim($_POST["pwd"]);

    // at least one of those is empty
    if (empty($email) || empty($password)) {

        // Redirect to login with email, if not empty, inside the placeholder
        header("Location: /login/?login=false&email=" . $email . "&m=2&s=warning");
        exit;

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect to login if invalid email
        header("Location: /login/?login=false&m=1&s=warning");
        exit;

    } else {

        $query = "SELECT * FROM user WHERE email = :email";

        $user = $pdo->prepare($query);
        $user->execute(['email' => $email]);

        if ($user->rowCount() != 1) {
            // Redirect to login if rowcount is not 1
            header("Location: /login/?login=false&email=" . $email . "&m=1&s=warning");
            exit;

        } else {
            $user_row = $user->fetch();
            $pwd = $user_row["password"];

            $pwd_peppered = hash_hmac($hash_algorithm, $password, $pepper);

            if (!password_verify($pwd_peppered, $pwd)) {
                // redirect to login if password don't match
                header("Location: /login/?login=false&email=" . $email . "&m=1&s=warning");
                exit;

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
                $_SESSION["password"] = $user_row["password"];

                // SITE SETTINGS: Session variables
                $query = "SELECT * FROM site_settings";
                $site_setts = $pdo->prepare($query);

                // For every Site Settings Session Variable, please check if it's set
                if ($site_setts->execute() ) {

                    if ($site_setts->rowCount() > 0) {

                        $site_row = $site_setts->fetch();
                        $_SESSION["site_name"] = $site_row["name"];
                        $_SESSION["site_sound_fx"] = $site_row["sound_fx"];
                        $_SESSION["site_voiceovers"] = $site_row["voiceovers"];
                        $_SESSION["site_terms_enable"] = $site_row["terms_enable"];
                        $_SESSION["site_terms_text"] = $site_row["terms_text"];
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

                // Current event session variable: Please check if it's set
                $query = "SELECT id FROM event ORDER BY id DESC LIMIT 1";
                $current_event = $pdo->prepare($query);
                if ($current_event->execute()) {
                     if ($current_event->rowCount() > 0) {
                         $_SESSION["current_event"] = $current_event->fetch()["id"];
                     }
                }

                // Redirect to welcome page
                header("Location: /login/welcome");
                exit;
            }

        }
    }

}
// array of splash images as [<key>], credit as [<key>][0] and the optimal background positioning as [<key>][1]
$background_images = array(
    'horse-3880448_1920.jpg' => array('Clarence Alford','right top'),
    'horses-3811270_1920.jpg' => array('Clarence Alford','center top'),
    'horses-3817727_1920.jpg' => array('Clarence Alford','right top'),
);
// Randomize the array keys to get a random image filename
$random_image = array_rand($background_images);

?>
{header}
    <main role="main" id="login_page" style="background-image: url('/images/photos/splash/<?php echo $random_image;?>');background-position:<?php echo $background_images[$random_image][1];?>">
        <form class="vertical-center animate__animated animate__fadeIn" id="login" method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <div id="logo_wrapper">
                <img id="logo" class="" src="/images/kc-logo.svg">
                <h1 id="logo_text">Keene Challenge</h1>
            </div>
            <div class="form-group">
                <label for="email" class="sr-only">Email address</label>
                <input type="email"  id="email" class="form-control" name="email" placeholder="email" required autofocus value=<?php echo $value ?>>
            </div>
            <div class="form-group">
                <label for="password" class="sr-only">Password</label>
                <input type="password" class="form-control" id="password" name="pwd" placeholder="password" required aria-describedby="passwordHelpBlock">
                <small id="passwordHelpBlock" class="form-text text-muted">
                    Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
                </small>
            </div>
            <input type="submit" value="Login" name="login" class="btn btn-primary btn-block">
            <div id="forgot_pwd">
                <a href="/password/">Forgot Password</a>
            </div>
        </form>
        <span id="photo_credit">Photo by <?php echo $background_images[$random_image][0];?></span>
    </main>
{footer}
<?php ob_end_flush(); ?>