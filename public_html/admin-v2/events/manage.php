<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Manage Event";

$debug = debug($_GET);

if (empty($_SESSION["id"])) {
	header("Location: /login/");
	exit;
}

if ($_SESSION["admin"] != 1) {
	header("Location: /races/");
	exit;
}

$eventId = (int) $_GET["e"];
$page = (int) $_GET["pg"];
$query = "SELECT name, status FROM event WHERE id=:eventId";
$stmt = $pdo->prepare($query);
$stmt->execute(['eventId' => $eventId]);
$data = $stmt->fetch();
$eventName = $data["name"];
$eventStatus = $data["status"];

// Event is closed, send to the view page.
if ($eventStatus == 1) {
	$headerString = "Location: ../events/view.php?e=" . $eventId . "&pg=" . $page;
	header($headerString);
}

?>

{header}
{main_nav}

<main role="main" id="admin_main_events_page">
	<div class="floating-alert alert  alert-dismissible fade show fixed-top mt-5 mx-4 d-none" role="alert" id="alert">
		<span id="msg">messages will be display here.</span>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<h1 class="sticky-top padded-top-header mb-5" id="manage-events-page-header">
		<a href="../events/" class="font-lighter">Events</a>
		<span class="font-lighter"> > </span>
		<span id="event-name"> <?php echo $eventName; ?></span>
	</h1>

	<form id="create-event" method="POST">
		<section>
			<!-- Event Name -->
			<div class="form-row">
				<div class="form-group col-md-6">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-horse-head"></i></span>
						</div>
						<input type="text" class="form-control" id="name" name="event_name" maxlength="25">
					</div>
				</div>
				<!-- Event Date -->
				<div class="form-group col-md-6">
					<div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-auto-close="true" data-date-today-highlight="true" data-date-orientation="auto" data-date-z-index-offset="2000" data-date-clear-btn="true" data-date-today-btn="true">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
						</div>
						<input type="text" class="form-control" id="date" name="event_date">
					</div>
				</div>
			</div>
			<!-- Event POT -->
			<div class="form-row">
				<div class="form-group col-md-6">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-donate"></i></span>
						</div>
						<input type="number" class="form-control" id="pot" name="event_pot" min="1" max="9999">
					</div>
				</div>
			</div>
		</section>
	</form>

	<h3 class="m-3">Races in this Event</h3>
	<p class="m-3 text-center">Races with results already entered may not be edited or reopened.</p>

	<div id="add-race-container">
		<p><a class="fas fa-plus-circle"></a><span>Add a race</span></p>
	</div>

	<ul id="races-list" class="list-group list-group-flush col-md-12">
	</ul>
	<div id="page-btns-container" style="margin: 0 !important;">
		<button id="prev-btn" class="btn btn-sm shadow-none ml-1 page-control-btn" style="display: none;">Previous</button>
		<button id="next-btn" class="btn btn-sm shadow-none ml-1 page-control-btn" style="display: block;">Next</button>
	</div>
</main>

<div id="race-loader">
	<div id="loader-container">
		<div class="lds-ring" id="loader">
			<div></div>
			<div></div>
			<div></div>
			<div></div>
		</div>
	</div>
</div>

{footer}

<script type="text/javascript" src="./js/manageEvent.js"></script>