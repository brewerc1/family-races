<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
$page_title = "Admin";
$javascript = <<<HERE
HERE;

// Authentication  and Authorization System
ob_start('template');
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

?>
{header}
{main_nav}
    <main role="main">
        <section>
            <h1>The Stables</h1>
            <ul >
                <li><a href="link to current event"> Current Event </a></li>
                <li><a href="link to event and race management page">Event & Race Managment</a></li>
                <li><a href="./users">User Management</a></li>
                <li><a href="./settings">Site Settings</a></li>
            </ul>
        </section>
     
    </main>

{footer}
<?php ob_end_flush(); ?>