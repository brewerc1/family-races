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
    /*I did not delete this as I did not write this. Leaving here, commented out, pending reply from Jonathan
        if ($_SESSION['admin']) {
            echo <<< ADMIN
    <li><a href= "http://localhost/admin/">Admin</a></li>
    ADMIN;
        }*/
        ?>

    <main role="main">
        <section>
            <h1>Admin</h1>
            <ul>
                <li><a href="/races/?e=<?php echo $_SESSION['current_event']; ?>">Current Event</a></li>
                <li><a href="./races/">Event & Race Managment</a></li>
                <li><a href="./users/">User Management</a></li>
                <li><a href="./settings/">Site Settings</a></li>
            </ul>
        </section> 
    </main>
    {footer}
    <?php ob_end_flush(); ?>