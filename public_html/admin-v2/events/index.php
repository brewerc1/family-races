<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Events";

$debug = debug();

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
	<h1 class="sticky-top" id="view-all-events-header"><?php echo $page_title;?></h1>
	<div id="loader-container">
			<div class="lds-ring" id="loader"><div></div><div></div><div></div><div></div></div>
		</div>
	<section id="events" class="mt-5">
		<div id="current-event-container">
			<h3 class="mb-3">Current Event</h3>
			<ul id="current-event-list" class="list-group list-group-flush col-md-12">
				<li id="has-current-event-warning" class="list-group-item"><small class="text-muted">To create a new event, you must close the current event.</small></li>
			</ul>
		</div>

		<div class="text-center mb-5 mt-3" id="create-event-container">
			<a id="create-event" class="btn btn-primary text-center" href="./create.php">Create New Event</a>
			<div><small class="text-muted">There is no current event.</small></div>
		</div>

		<div class="justify-content-center row mt-5" id="events-list-container">
			<h3 class="mb-3">Past Events</h3>
			<ul id="events-list" class="list-group list-group-flush col-md-12"></ul>
		</div>

		<div id="page-btns-container">
			<button id="prev-btn" class="btn btn-sm mr-1 shadow-none page-control-btn" style="display: none;">Previous</button>
			<button id="next-btn" class="btn btn-sm ml-1 shadow-none page-control-btn" style="display: none;">Next</button>
		</div>
	</section>
</main>
{footer}
<?php ob_end_flush(); ?>

<script type="text/javascript" src="js/displayEvents.js"></script>