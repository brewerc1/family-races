<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
?>

<h1>Races Page</h1>
<div
    <ul>
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
            <input type="number" step=1>
            <p>to</p>
            <ul>
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