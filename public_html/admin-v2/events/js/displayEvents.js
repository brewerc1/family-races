const state = {
  firstPage: "http://localhost/api/events/?pg=1",
  nextPage: null,
  previousPage: null,
  hasCurrentEvent: false,
};

function fetchEvents(requestURL) {
  $.get(requestURL, (data) => {
    const events = data.data.events;

    if (data.data.next) state.nextPage = `http://${data.data.next}`;
    else state.nextPage = null;

    if (data.data.previous) state.previousPage = `http://${data.data.previous}`;
    else state.previousPage = null;

    addEventsToDOM(events);
    console.log(state);
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

    if (event.status === 0) state.hasCurrentEvent = true;

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

  if (!state.hasCurrentEvent) {
    $("#create-event").removeClass("disabled");
    $("#has-current-event-warning").css("display", "none");
    return;
  } else {
    // This else clause should be unneccesary once the API is fixed to send events in order
    $("#create-event").addClass("disabled");
    $("#has-current-event-warning").css("display", "block");
  }
}

function toggleButtonVisibility() {
  if (state.nextPage === null) $("#next-btn").css("display", "none");
  else $("#next-btn").css("display", "block");

  if (state.previousPage === null) $("#prev-btn").css("display", "none");
  else $("#prev-btn").css("display", "block");
}

function nextPage() {
  if (state.nextPage === null) return;
  console.log("here with nextpage " + state.nextPage);
  fetchEvents(state.nextPage);
}

function previousPage() {
  if (state.previousPage === null) return;
  fetchEvents(state.previousPage);
}

$(document).ready(fetchEvents(state.firstPage));

$("#next-btn").on("click", nextPage);
$("#prev-btn").on("click", previousPage);
