<?php
return <<< HTML
    <nav id="main-navigation" class="navbar navbar-expand-sm fixed-bottom navbar-light bg-light">
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
                <li class="nav-item" id="profile">
                    <a class="nav-link" href="/profile/">Me</a>
                </li>
                <li class="nav-item" id="admin">
                    <a class="nav-link" href="/admin/">Admin</a>
                </li>
            </ul>
        </div>
    </nav>
HTML;
?>