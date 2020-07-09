<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
$page_title = "Admin";
$javascript = <<<HERE
HERE;
// Authentication  and Authorization System
ob_start('template');
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");

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

       <?php
//Does this need to be here or added to the header file?
       if ($_SESSION['admin']) {
            echo <<< ADMIN
<li><a href= "http://localhost/admin/">Admin</a></li>
ADMIN;
        }
        ?>
    <main role="main">
        <section>
            <h1>Admin</h1>
            <ul>
                <li><a href="link to current event"> Current Event </a></li>
                <li><a href="link to event and race management page">Event & Race Managment</a></li>
                <li><a href="./users">User Management</a></li>
                <li><a href="./settings">Site Settings</a></li>
            </ul>
        </section> 
    </main>
    {footer}
    <?php ob_end_flush(); ?>