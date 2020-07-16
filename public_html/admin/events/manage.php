<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication  and Authorization System
ob_start();
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
    <h1>Manage an Event</h1>
    <h2>Event Name</h2>
    <h2>Event Date</h2>
    <form>
        <section>
            <label>Jackpot:</label>
            <input type="text" name="Jackpot amount"/>
        </section>
        <section>
            <label>Video:</label>
            <input type="text" name="video"/>
        </section>
    <h5>Races</h5>
        <section>
            <select name="race" id="race">
            <!--number of horses, horse numbers and window status need to be added. form within a form not happening-->
                <option value="race 1">Race 1</option>
                <option value="race 2">Race 2</option>
                <option value="race 3">Race 3</option>
                <option value="race 4">Race 4</option>
                <option value="race 5">Race 5</option>
                <option value="race 6">Race 6</option>
                <option value="race 7">Race 7</option>
                <option value="race 8">Race 8</option>
                <option value="race 9">Race 9</option>
                <option value="race 10">Race 10</option>
                <option value="race 11">Race 11</option>
                <option value="race 12">Race 12</option>
            </select>
        </section>
        <input type="submit" value="Save Event"/>
    </form>
{footer}
<?php ob_end_flush(); ?>