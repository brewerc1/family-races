const params = new URLSearchParams(window.location.search);

let loading = true;

const eventNameHeader = $("#event-name");
const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");

function fetchEvent() {
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
  // const url = `http://localhost/api/races?e=${params.get("e")}`;
  // $.get(url, (data) => {
  //   console.log(data);
  // });
}

function handleOnChange() {
  if (loading) return;

  const requestURL = `http://localhost/api/events?e=${params.get("e")}`;

  const data = {
    name: nameField.val(),
    date: dateField.val(),
    pot: Number.parseFloat(potField.val()),
  };

  console.log(typeof data.pot);

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

function restrictNumberRange() {
  let value = parseInt(potField.val());
  let min = parseFloat(potField.attr("min"));
  let max = parseFloat(potField.attr("max"));

  if (value < min) {
    potField.val(min);
  } else if (value > max) {
    potField.val(max);
  }
}

$(document).ready(fetchEvent);

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);

potField.on("keyup", restrictNumberRange);
