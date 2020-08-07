<?php
/**
 * Page to display Races
 * 
 * This page displays races of the current event.
 * Logged in users can place bets and view results.
 * User data for logged in user is stored in $_SESSION.email
 * Page checks for $_GET
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Test for authorized user
if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

// Set the page title for the template
$page_title = "Welcome";

// Include the race picker javascript
$javascript = '';

// Get UID
$uid = $_SESSION['id'];

///// DEBUG
//$debug = debug();
///// end DEBUG

?>
{header}
{main_nav}
<main role="main" id="welcome_page">
    <h1 class="sticky-top">Welcome to <?php echo $_SESSION['site_name'];?></h1>
    <div class="embed-responsive embed-responsive-16by9">
        <iframe class="embed-responsive-item" src="<?php echo $_SESSION['site_welcome_video_url'];?>" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="text-center">
        <a href="/races/" class="btn btn-primary mt-2">Skip</a>
    </div>
</main>
{footer}
<?php ob_end_flush(); ?>
