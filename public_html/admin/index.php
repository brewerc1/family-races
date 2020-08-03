<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Test for authorized user
if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

//$debug = debug();


// Set the page title for the template
$page_title = "Admin";

$javascript = '';
?>
{header}
{main_nav}
    <main role="main" id="admin_page">
		<h1 class="mb-5 sticky-top">Admin</h1>
		<section>
			<ul class="list-unstyled text-center mt-5">
				<li><a class="btn btn-primary mb-4" href="/races/?e=<?php echo $_SESSION['current_event']; ?>">Current Event</a></li>
				<li><a class="btn btn-primary mb-4" href="./events/">Event &amp; Race Managment</a></li>
				<li><a class="btn btn-primary mb-4" href="./users/">User Management</a></li>
				<li><a class="btn btn-primary mb-4" href="./settings/">Site Settings</a></li>
			</ul>
		</section> 
	</main>
{footer}
<?php ob_end_flush(); ?>