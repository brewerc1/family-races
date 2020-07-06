<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

return <<<HTML
    <!--The main navigation menu to be displayed on most pages. Not all links work yet.-->
    <nav id="main-navigation" class="navbar navbar-expand-sm fixed-bottom navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggle" aria-controls="navbarToggle" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class=" navbar-nav mr-auto">
            <li class="nav-item" id="races"><a href="http://localhost/races/index.php">Races</a></li>
            <li class="nav-item" id="hof"><a href="http://localhost/hof/index.php">HOF</a></li>
            <li class="nav-item" id="faq"><a href="http://localhost/faq/index.php">FAQ</a></li>
            <li class="nav-item" id="profile"><a href="http://localhost/profile/index.php">Me</a></li>
            <li class="nav-item" id="admin"><a href="http://localhost/admin/index.php">Admin</a></li>
        </ul>
    </nav>
HTML;
?>