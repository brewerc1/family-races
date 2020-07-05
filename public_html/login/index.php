<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

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
                ob_start();
                session_start();

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
if (isset($_GET["message"])) {
    $notification = $_GET["message"];
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Log In</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<main role="main">
    <form method="POST" action= <?php $_SERVER["PHP_SELF"] ?>>
        <input type="email" name="email" placeholder="someone@something.com"
               value=<?php echo $value ?>>
        <input type="password" name="pwd" placeholder="password">
        <input type="submit" value="Login" name="login">
    </form>
    <div>
        <!-- Notification: use css or javascript to display only for few minutes-->
        <span class="notification">
                <?php
                echo $notification;
                ?>
            </span>
    </div>
    <div id="forgot_pwd">
        <a href="http://localhost/password">Forgot Password</a>
    </div>
</main>


<footer>
    <p>Created by students of the College of Informatics at Northern Kentucky University</p>
</footer>
</body>
</html>