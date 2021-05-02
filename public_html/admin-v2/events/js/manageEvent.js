const params = new URLSearchParams(window.location.search);

let loading = true;

// HTML Elements
const eventNameHeader = $("#event-name");
const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");

function fetchEvent() {
  // Only current event can be updated
  if (params.get("status") == 1) {
    nameField.prop("disabled", true);
    dateField.prop("disabled", true);
    potField.prop("disabled", true);
  }

  displayEventInformation();
  displayEventRaces();

  loading = false;
  // Display error if none
}

function displayEventInformation() {
  const eventName = params.get("name");
  const eventPot = params.get("pot");
  const eventDate = params.get("date");

  eventNameHeader.text(eventName);
  nameField.val(eventName);
  dateField.val(eventDate);
  potField.val(eventPot);
}

function displayEventRaces() {
  console.log("displaying races");
}

// Only current event can be updated
function handleOnChange() {
  if (loading) return;

  const requestURL = `http://localhost/api/events?e=${params.get("e")}`;

  const data = {
    name: nameField.val(),
    date: dateField.val(),
    pot: potField.val(),
  };

  $.ajax({
    type: "PUT",
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(data),
  }).done(useNewEventData);
}

function useNewEventData() {
  params.set("name", nameField.val());
  params.set("date", dateField.val());
  params.set("pot", potField.val());

  let newURL = `${window.location.pathname}?${params.toString()}`;
  history.pushState(null, "", newURL);

  eventNameHeader.text(nameField.val());
}

$(document).ready(fetchEvent);

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);
