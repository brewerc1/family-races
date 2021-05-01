<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Events";

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
<main role="main" id="admin_events_page">
	<h1 class="mb-5 sticky-top">Events</h1>
	<section id="events" class="mt-5">
		<div class="text-center mb-1 mt-3" id="create-event-container">
			<a id="create-event" class="btn btn-primary text-center disabled" href="./create.php">Create New Event</a>
		</div>
		<div class="justify-content-center row mt-5">
			<ul id="events-list" class="list-group list-group-flush col-md-6"></ul>
		</div>
		<div class="justify-content-center row mt-5">
			<!-- Hidden by default -->
			<button id="prev-btn" class="btn btn-sm btn-outline-primary mr-1" style="display: none;">Previous</button>
			<button id="next-btn" class="btn btn-sm btn-outline-primary ml-1" style="display: none;">Next</button>
		</div>
	</section>
</main>
{footer}
<?php ob_end_flush(); ?>

<script type="text/javascript" src="js/displayEvents.js"></script>