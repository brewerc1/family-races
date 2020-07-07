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
//$value = "";
//if (!empty($_GET["email"])) {
//    $value = $_GET["email"];
//}

if (isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $password = trim($_POST["pwd"]);

    if (empty($email) || empty($password)) {

        // Redirect to login with email, if not empty, inside the placeholder
        $notification = "Invalid Credentials";
        header("Location: /login/");

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect to login if invalid email
        $notification = "Invalid Credentials";
        header("Location: /login/");

    } else {

        $query = "SELECT * FROM user WHERE email = :email";

        $user = $pdo->prepare($query);
        $user->execute(['email' => $email]);

        if ($user->rowCount() != 1) {
            // Redirect to login if rowcount is not 1
            $notification = "Invalid Credentials";
            header("Location: /login/");

        } else {
            $user_row = $user->fetch();
            $pwd = $user_row["password"];

            $pwd_peppered = hash_hmac($hash_algorithm, $password, $pepper);

            if (!password_verify($pwd_peppered, $pwd)) {
                // redirect to login if password don't match
                $notification = "Invalid Credentials";
                header("Location: /login/");

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
                $_SESSION["sound_fx"] = $user_row["sound_fx"] == 1; //bool
                $_SESSION["voiceovers"] = $user_row["voiceovers"] == 1; //bool
                $_SESSION["admin"] = $user_row["admin"] == 1; //bool

                // Redirect to welcome page
                header("Location: /login/welcome/");
            }

        }
    }

}

// Notification System
$notification = "";

?>
{header}
    <main role="main">
        <form method="POST" action=<?php $_SERVER["PHP_SELF"] ?>>
            <input type="email" class="is-invalid" id="validationServer01" name="email" placeholder="Email" required>
            <input type="password" class="is-invalid" id="validationServer02" name="pwd" placeholder="Password" required>
            <div class="invalid-feedback">
                <?php echo $notification ?>
            </div>
            <input type="submit" name="login" value="Log In">
        </form>
        <div id="forgot_pwd">
            <a href="/password/">Forgot Password</a>
        </div>
    </main>
{footer}
<?php ob_end_flush(); ?>