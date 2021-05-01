const state = { eventInfo: null, loading: true };

const eventNameHeader = $("#event-name");
const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");

function getUrlVars() {
  const url = window.location.href;
  const vars = {};

  url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
    key = decodeURIComponent(key);
    value = decodeURIComponent(value);
    vars[key] = value;
  });

  return vars;
}

function fetchEvent() {
  // Get query string
  const queryStringParams = getUrlVars();
  state.eventInfo = queryStringParams;

  // Fetch this events races
  // const requestURL = `http://localhost/api/races?e=${state.currentEvent.e}`;
  // $.get(requestURL, (data) => {
  //   console.log(data);
  // });

  displayEventInformation();
  // displayEventRaces();

  state.loading = false;
  // Display error if none
}

function displayEventInformation() {
  const eventName = state.eventInfo.name;
  const eventPot = state.eventInfo.pot;
  const eventDate = state.eventInfo.date;

  eventNameHeader.text(eventName);
  nameField.val(eventName);
  dateField.val(eventDate);
  potField.val(eventPot);
}

function handleOnChange() {
  if (state.loading) return;

  const requestURL = `http://localhost/api/events?e=${state.eventInfo.e}`;

  const data = {
    name: nameField.val(),
    date: dateField.data("datepicker").getFormattedDate("yyyy-mm-dd"), // Need to figure this out
    pot: potField.val(),
  };

  console.log(dateField.val());

  $.ajax({
    type: "PUT",
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(data),
  }).done(() => {
    // Can simplify this when API supports getting 1 event
    state.eventInfo.name = nameField.val();
    state.eventInfo.date = dateField.val();
    state.eventInfo.pot = potField.val();
    eventNameHeader.text(nameField.val());
  });
}

$(document).ready(fetchEvent);

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);
