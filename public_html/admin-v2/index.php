<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} 

if ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}
 
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

// Set the page title for the template
$page_title = "Admin";

$debug = debug();

$javascript = '';

?>
{header}
{main_nav}
    <main role="main" id="admin_page">
		<h1 class="mb-5 sticky-top">Admin</h1>
		<section>
			<ul class="list-unstyled text-center mt-5">
				<li><a class="btn btn-primary mb-4" href="./events/">Event &amp; Race Managment</a></li>
				<li><a class="btn btn-primary mb-4" href="../admin/users/">User Management</a></li>
				<li><a class="btn btn-primary mb-4" href="../admin/settings/">Site Settings</a></li>
			</ul>
		</section> 
	</main>
{footer}
<?php ob_end_flush(); ?>