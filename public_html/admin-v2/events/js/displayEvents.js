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
  const currentEventlist = $("#current-event-list");
  const createEventContainer = $("#create-event-container");
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
    const template = `
    <li class="list-group-item" id=${event.id}>
      <div class="flex-container">
        <p class="event-title">
          ${event.name}
        </p>
        <a class="view-event-btn" href="./manage.php?e=${event.id}">
          View
        </a>
      </div>
    </li>
    `;

    if (event.status === 0) {
      state.hasCurrentEvent = true;
      $("#current-event-container").css("display", "block");
      currentEventlist.append(template);
      return;
    }

    eventsList.append(template);
  });

  // This currently always shows because the API pagination is broken, should resolve when the API is fixed
  if (!state.hasCurrentEvent) createEventContainer.css("display", "block");
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
