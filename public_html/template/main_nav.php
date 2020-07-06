<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

return <<<HTML
    <!--The main navigation menu to be displayed on most pages. Not all links work yet.-->
    <nav id="main-navigation">
        <h1>Main Navigation</h1>
        <ul>
            <li><a href="http://localhost/races/index.php">Races</a></li>
            <li><a href="http://localhost/HOF/index.php">HOF</a></li>
            <li><a href="http://localhost/FAQ/index.php">FAQ</a></li>
            <li><a href="http://localhost/profile/index.php">Me</a></li>
            <li><a href="http://localhost/admin/index.php">Admin</a></li>
        </ul>
    </nav>
HTML;
?>