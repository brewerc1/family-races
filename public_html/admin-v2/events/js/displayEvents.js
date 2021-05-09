const loader = $("#loader-container");

const state = {
  firstPage: "http://localhost/api/events/?pg=1",
  nextPage: null,
  previousPage: null,
  hasCurrentEvent: false,
  currentEventID: null,
};

function fetchEvents(requestURL) {
  $.get(requestURL, (data) => {
    const events = data.data.events;

    if (data.data.next) state.nextPage = `http://${data.data.next}`;
    else state.nextPage = null;

    if (data.data.previous) state.previousPage = `http://${data.data.previous}`;
    else state.previousPage = null;

    addEventsToDOM(events);
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

  let eventsProcessed = 0;

  events.forEach((event) => {
    if (event.id === state.currentEventID) return;

    let template = `
    <li class="list-group-item" id=${event.id}>
      <div class="flex-container">
        <p class="event-title">
          ${event.name}
        </p>
        <a class="black-btn" href="./manage.php?e=${event.id}&name=${event.name}&date=${event.date}&pot=${event.pot}&status=${event.status}">
          View
        </a>
      </div>
    </li>
    `;

    if (event.status === 0 && !state.hasCurrentEvent) {
      state.hasCurrentEvent = true;
      state.currentEventID = event.id;
      $("#current-event-container").css("display", "block");

      template = `
      <li class="list-group-item" id=${event.id}>
        <div class="flex-container">
          <p class="event-title">
            ${event.name}
          </p>
          <div class="current-event-controls">
            <a class="black-btn" href="" id="admin-close-event-btn">
            Close Event
            </a>
            <a class="black-btn" href="./manage.php?e=${event.id}&name=${event.name}&date=${event.date}&pot=${event.pot}&status=${event.status}">
            View
            </a>
          </div>
        </div>
      </li>
      `;
      currentEventlist.append(template);

      // Add event listener
      $("#admin-close-event-btn").on("click", (e) => closeEvent(e));
    } else {
      eventsList.append(template);
    }

    eventsProcessed++;

    if (eventsProcessed === events.length) toggleLoader();
  });

  if (!state.hasCurrentEvent) createEventContainer.css("display", "block");
}

function closeEvent(e) {
  e.preventDefault();
  let canCloseEvent = confirm("Are you sure you want to close this event?");

  if (!canCloseEvent) return;
}

function toggleLoader() {
  console.log("toggling");
  loader.css("display", "none");
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
