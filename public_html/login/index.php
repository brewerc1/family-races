<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// set the page title for the template
$page_title = "Login";

// include the menu javascript for the template
$javascript = <<< HERE
$( document ).ready(function() {
    $('#hof').addClass('active');
});
HERE;


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

                // Session variables (13)
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
{main_nav}
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