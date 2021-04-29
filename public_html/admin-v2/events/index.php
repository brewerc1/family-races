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

<script>

	const firstPage = "http://localhost/api/events?e=0";
	const state = { nextPage: null, previousPage: null, hasCurrentEvent: false };
	
	// Fetches events from API
	// Updates app state
	const fetchEvents = requestURL => {
		$.get(requestURL, data => {
			const events = data.data.events;
			state.nextPage = data.data.next;
			state.previousPage = data.data.previous;
			addEventsToDOM(events);
		});
	}

	// Removes all current events (if any) from DOM
	// Adds new events to the DOM
	const addEventsToDOM = events => {
		const eventsList = $("#events-list");
		eventsList.empty();

		// Only toggles if neccessary
		toggleButtonVisibility();

		// There are no events
		if(events.length === 0) {
			const alert = '<p class="alert alert-info" role="alert">No events have been created.</p>'
			eventsList.append(alert);
		}

		// There is only 1 event & events is an Object instead of an array
		if(events.id) events = [events];

		events.forEach(event => {

			// Class name and message needed for template below
			const eventStatusMessage = event.status == 1 ? "closed" : "current";
			const eventStatusClass = event.status == 1 ? "info" : "success";

			// There is a current event
			if(event.status === 0) { 
				state.hasCurrentEvent = true;
				const hasCurrentEventMessage = '<div><small class="text-muted">To create a new event, you must close the current event.</small></div>'
				$('#create-event-container').append(hasCurrentEventMessage);
			}

			const template = `
			<li class='list-group-item'>
				<a href='./manage.php?e=${event.id}'>
					${event.name}
					<span class="px-2 status_badge badge badge-pill float-right badge-${eventStatusClass}">
						${eventStatusMessage}
					</span>
				</a>
			</li>
			`;

			eventsList.append(template);
		});

		// Needs to be done this way instead of adding it to prevent bugs
		if(!state.hasCurrentEvent) $("#create-event").removeClass("disabled");

	}
	
	// Toggles Next & Previous Button Visibility
	const toggleButtonVisibility = () => {
		if(state.nextPage === null) $("#next-btn").css("display", "none");
		else $("#next-btn").css("display", "block");

		if(state.previousPage === null) $("#prev-btn").css("display", "none");
		else $("#prev-btn").css("display", "block");
	}

	// Loads the next page
	const nextPage = () => {
		if(state.nextPage === null) return;
		fetchEvents(state.nextPage);
	}	

	// Loads the previous page
	const previousPage = () => {
		if(state.previousPage === null) return;
		fetchEvents(state.previousPage);
	}

	// Get first page on load
	$(document).ready(fetchEvents(firstPage));

	// Next and previous page
	$("#next-btn").on("click", nextPage);
	$("#prev-btn").on("click", previousPage)


</script>
