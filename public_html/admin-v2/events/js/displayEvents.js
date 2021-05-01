const firstPage = "http://localhost/api/events?e=0";
const state = { nextPage: null, previousPage: null, hasCurrentEvent: false };

function fetchEvents(requestURL) {
  $.get(requestURL, (data) => {
    const events = data.data.events;
    state.nextPage = data.data.next;
    state.previousPage = data.data.previous;
    addEventsToDOM(events);
  });
}

function addEventsToDOM(events) {
  const eventsList = $("#events-list");
  eventsList.empty();

  toggleButtonVisibility();

  if (events.length === 0) {
    const alert =
      '<p class="alert alert-info" role="alert">No events have been created.</p>';
    eventsList.append(alert);
  }

  // Turn events into an array if there is only 1
  if (events.id) events = [events];

  events.forEach((event) => {
    const eventStatusMessage = event.status == 1 ? "closed" : "current";
    const eventStatusClass = event.status == 1 ? "info" : "success";

    if (event.status === 0) {
      state.hasCurrentEvent = true;
      const hasCurrentEventMessage =
        '<div><small class="text-muted">To create a new event, you must close the current event.</small></div>';
      $("#create-event-container").append(hasCurrentEventMessage);
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

  if (!state.hasCurrentEvent) $("#create-event").removeClass("disabled");
}

function toggleButtonVisibility() {
  if (state.nextPage === null) $("#next-btn").css("display", "none");
  else $("#next-btn").css("display", "block");

  if (state.previousPage === null) $("#prev-btn").css("display", "none");
  else $("#prev-btn").css("display", "block");
}

function nextPage() {
  if (state.nextPage === null) return;
  fetchEvents(state.nextPage);
}

function previousPage() {
  if (state.previousPage === null) return;
  fetchEvents(state.previousPage);
}

$(document).ready(fetchEvents(firstPage));

$("#next-btn").on("click", nextPage);
$("#prev-btn").on("click", previousPage);
