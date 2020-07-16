<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication  and Authorization System
ob_start();
session_start();

/*if (!isset($_SESSION["id"])) {
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
}*/

?>
{header}
{main_nav}
     <h1>Create an Event</h1>
        <form>
            <ul style="list-style-type:none;">
                <li><input style="text" id="event name" value="Event Name"/></li>
                <li><input style="text" id="date" value="Date"/></li>
                <li><input style="text" id="jackpot" value="Jackpot"/></li>
                <li><input style="text" id="vid url" value="Welcome Video URL"/></li>
            
                <li><label>Betting windows open sequentially</label>
                <input type="checkbox">
                <span class="slider round"></span></li>
                <!--will need styled in css to show slider vs checkbox-->

                <li><input type="submit" value="Create Event"/></li>
            </ul>
        </form>
{footer}
<?php ob_end_flush(); ?> 