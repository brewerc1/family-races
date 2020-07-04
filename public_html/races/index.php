<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");


include '../template/header.php';

?>

<h1>Races Page</h1>
<div
    <ul id="race-dropdown">
        <li> Race 1 </li>
        <li> Race 2 </li>
        <li> Race 3 </li>
        <li> Race 4 </li>
        <li> Race 5 </li>
    </ul>

    <div id="betting-open">
        <h2>Place your bet:</h2>
        <form>
            <p>Horse:</p>
            <input type="number" step=1 name="horse-number">
            <p>to</p>
            <ul id="horse-position" name="horse-position">
                <li> Win </li>
                <li> Place </li>
                <li> Show </li>
            </ul>
            <input type="submit" value="Place Bet">
        </form>
    </div>
    <div id="betting-closed">
        <!-- need to add some php to determine whether or not we have an img for the race -->
        <img src="" alt="Default image">
        <h2>The betting window has closed.</h2>
        <h2>Check back for results after the race!</h2>
    </div>
</div>

<?php
include '../template/footer.php';
?>