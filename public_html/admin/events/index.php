<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Events";

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
$debug = debug();

?>
{header}
{main_nav}

    <main role="main">
        <section>
            <h1>Events</h1>
            <ul class="list-unstyled text-center mt-5">
                <li><a class="btn btn-primary mb-4" href="./create.php">Create New Event</a></li>
            </ul>

            <h2>Previous Events</h2>
            <ul class="list-unstyled text-center mt-3">
                <li><a href="#">Reunion 2021</a> <span class='badge badge-primary badge-pill' id='invited_badge'>completed</span></li>
            </ul>
        </section>
    </main>

{footer}
<?php ob_end_flush(); ?>
