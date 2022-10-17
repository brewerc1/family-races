<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Turn on output buffering
ob_start('template');

// Echo hashed password for testing
//echo password_hash(hash_hmac($hash_algorithm, "password1", $pepper), PASSWORD_BCRYPT);

// Set the page title for the template
$page_title = "Login";

$debug = debug();

// Include the menu javascript for the template
$javascript = "";

$value = "";
if (!empty($_GET["email"])) {
    $val = trim($_GET["email"]);
    $value = filter_var($val, FILTER_VALIDATE_EMAIL) ? $val : "";
}

if (isset($_POST["login"])) {

    try {

        $email = trim($_POST["email"]);
        $password = trim($_POST["pwd"]);


        if (empty($email) || empty($password)) {
            header("Location: /login/?login=false&email=" . $email . "&m=2&s=warning");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: /login/?login=false&m=1&s=warning");
            exit;
        }


        $query = "SELECT * FROM user WHERE email = :email";
        $user = $pdo->prepare($query);
        $user->execute(['email' => $email]);


        if ($user->rowCount() != 1) {
            header("Location: /login/?login=false&email=" . $email . "&m=1&s=warning");
            exit;
        }


        $user_row = $user->fetch();
        $pwd = $user_row["password"];
        $pwd_peppered = hash_hmac($hash_algorithm, $password, $pepper);
        if (!password_verify($pwd_peppered, $pwd)) {
            header("Location: /login/?login=false&email=" . $email . "&m=1&s=warning");
            exit;
        }

        if ($user_row["inactive"]) {
            header("Location: /login/?login=false&m=24&s=warning");
            exit;
        }

        // USER: Session variables (13)
        foreach ($user_row as $user_session_key => $user_session_val)
            $_SESSION[$user_session_key] = $user_session_val;


        // SITE SETTINGS: Session variables
        $query = "SELECT * FROM site_settings";
        $site_setts = $pdo->prepare($query);
        $site_setts->execute();

        if ($site_setts->rowCount() > 0) {

            $site_row = $site_setts->fetch();
            foreach ($site_row as $site_session_key => $site_session_val)
                $_SESSION["site_" . $site_session_key] = $site_session_val;
        }


//        $query = "SELECT id FROM event ORDER BY id DESC LIMIT 1";
//        $current_event = $pdo->prepare($query);
//        $current_event->execute();
//
//        if ($current_event->rowCount() > 0) {
//            $_SESSION["current_event"] = $current_event->fetch()["id"];
//        }
        $_SESSION["current_event"] = getCurrentEventId($pdo);


        header("Location: /login/welcome");
        exit;





    } catch (Exception $e) {
        header("Location: /login/?login=false&email=" . $email . "&m=6&s=warning");
        exit;
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
        <form class="vertical-center animate__animated animate__fadeIn col-8 col-sm-4" id="login" method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <div id="logo_wrapper">
                <img id="logo" class="" src="/images/kc-logo.svg">
                <h1 id="logo_text"><?php echo $_SESSION['site_name'];?></h1>
            </div>
            <div class="form-group">
                <label for="email" class="sr-only">Email address</label>
                <input type="email"  id="email" class="form-control" name="email" placeholder="email" required autofocus value=<?php echo $value ?>>
            </div>
            <div class="form-group">
                <label for="password" class="sr-only">Password</label>
                <input type="password" class="form-control" id="password" name="pwd" placeholder="password" required aria-describedby="passwordHelpBlock">
            </div>
            <input type="submit" value="Login" name="login" class="btn btn-primary btn-block">
            <div id="forgot_pwd" class="text-secondary d-block mt-2 text-center">
                <a class="btn btn-text" href="/password/">Forgot Password?</a>
            </div>
        </form>
		<div class="navbar-text" id="development_credit">
			<span class="d-none d-md-block fade-in">Created by <a href="/credits/">College of Informatics students</a> at Northern Kentucky University</span>
			<span class="d-md-none d-block fade-in">Created by NKU <a href="/credits/">College of Informatics students</a></span>
			<span class="fade-in" id="photo_credit">Photo by <?php echo $background_images[$random_image][0];?></span>
		</div>
	</main>
{footer}
<?php ob_end_flush(); ?>
