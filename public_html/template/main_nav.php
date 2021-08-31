<?php
$nav = <<< HTML
    <nav id="main_navigation" class="navbar navbar-expand-sm fixed-bottom navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggle" aria-controls="navbarToggle" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarToggle">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item" id="races">
                    <a class="nav-link" href="/races/">Races</a>
                </li>
                <li class="nav-item" id="hof">
                    <a class="nav-link" href="/hof/">HOF</a>
                </li>
                <li class="nav-item" id="faq">
                    <a class="nav-link" href="/faq/">FAQ</a>
                </li>
                <li class="nav-item" id="user">
                    <a class="nav-link" href="/user/">Me</a>
                </li>
 
HTML;

if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1){
$nav .= <<< HTML
                <li class="nav-item" id="admin">
                    <a class="nav-link" href="/admin-v2/">Admin V2</a>
                </li>

HTML;
}
$nav .= <<< HTML
                <li class="nav-item" id="logout">
                    <a class="nav-link" href="#" 
                        data-toggle="modal" 
                        data-target="#mainModal" 
                        data-title="Logout" 
                        data-message="Are you sure you want to log out?"
                        data-button-primary-text="Logout" 
                        data-button-primary-action="window.location.href='/logout/'" 
                        data-button-secondary-text="Cancel" 
                        data-button-secondary-action="" 
                    >Logout</a>
                </li>
            </ul>
            <span class="navbar-text">
                <span class="d-none d-md-block fade-in">Created by <a href="/credits/">College of Informatics students</a> at Northern Kentucky University</span>
                <span class="d-md-none fade-in">Created by NKU <a href="/credits/">College of Informatics students</a></span>
            </span>
        </div>
    </nav>
HTML;
return $nav;
