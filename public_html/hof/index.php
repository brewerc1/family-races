<?php
/**
 * Page to display Hall of Fame
 * 
 * This page displays the Hall of Fame for the current event, and all prior events.
 * Logged in users view this page.
 */

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

// Set the page title for the template
$page_title = "Hall of Fame";

// Include the race picker javascript
$javascript = '';

///// DEBUG
$debug = debug("UID: $uid<br>Event: $event");
///// end DEBUG
?>
{header}
{main_nav}
    <main role="main">
    <h1>Hall of Fame</h1>
    <section>
        <h2>Current Champion</h2>
        <a href="/.user/"><img src="/images/no-user-image.jpg" alt="Photo of HOF winner" width="100" height="100"></a>
        <p>champion name  purse</p>
    </section>
    <section>
        <h2>Prior Champions</h2>
        <!--loop to show prior winners events, pic, name and purse-->
        <ul class="user-list list-group list-group-flush" id="race_leaderboard">
            <li class="list-group-item">
                <div class="media">
                    <a href="/user/?u={$row["id"]}">
                        <img src="$photo" alt="$alt" class="rounded-circle">
                    </a>
                    <div class="media-body"><span class="user_name d-inline-block px-3">$name</span> {$invited}</div>
                </div>
            </li>
        </ul>
    </section>

{footer}
<?php ob_end_flush(); ?>
