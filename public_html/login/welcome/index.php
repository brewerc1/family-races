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

// Test for authorized user
if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
}

// Test if video URL is present; if not, forward onward to races
if (empty($_SESSION["site_welcome_video_url"])) {
    header("Location: /races/");
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
        <a href="/races/" class="btn btn-primary mt-3 mb-3">Skip</a>
    </div>
</main>
{footer}
<?php ob_end_flush(); ?>
