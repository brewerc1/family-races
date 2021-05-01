<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Manage Event";

if (empty($_SESSION["id"])) {
	header("Location: /login/");
	exit;
}

if ($_SESSION["admin"] != 1) {
	header("Location: /races/");
	exit;
}

?>

{header}
{main_nav}

<main role="main" id="admin_main_events_page">
    <h1 class="sticky-top padded-top-header" id="manage-events-page-header">Events >  </h1>

</main>

{footer}

<script type="text/javascript" src="./js/manageEvent.js"></script>