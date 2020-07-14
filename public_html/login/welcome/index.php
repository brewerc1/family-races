<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start();
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /login/");

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");

}

// Testing
echo "The current event id is " . $_SESSION["current_event"];
?>

<h1>Welcome <?php echo $_SESSION["first_name"] . " " . $_SESSION["last_name"] ?></h1>


<?php
// Testing the admin
    if ($_SESSION["admin"])
        echo "<h2> You are an Admin </h2>";
    else
        echo "<h2> You are not an Admin </h2>";
?>

<p>Displays the welcome video: <?php echo $_SESSION["site_welcome_video_url"] ?></p>
