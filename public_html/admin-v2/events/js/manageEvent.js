const params = new URLSearchParams(window.location.search);

let loading = true;

let invalidFields = false;

const eventNameHeader = $("#event-name");
const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const loader = $("#loader-container");

function fetchEvent() {
  displayEventInformation();
  displayEventRaces();

  loading = false;
}

function displayEventInformation() {
  const requestURL = `http://localhost/api/events?e=${params.get("e")}`;
  $.get(requestURL, (data) => {
    const event = data.data.events[0];
    const eventName = event.name;
    const eventPot = Number.parseFloat(event.pot);
    const eventDate = event.date;

    eventNameHeader.text(eventName);
    nameField.val(eventName);
    dateField.val(eventDate);
    potField.val(eventPot);
  });
}

function displayEventRaces() {
  let racesProcessed = 0;
  const racesList = $("#races-list");
  const requestURL = `http://localhost/api/races?e=${params.get("e")}`;
  $.get(requestURL, (data) => {
    const races = data.data.races;

    if (races.length === 0) toggleLoader();

    races.forEach((race) => {
      const template = `
      <li class="list-group-item" id="${race.race_number}">
        <div class="flex-space-between">

          <div class="event-title-container"> 
            <p class="event-title">
              Race ${race.race_number}
            </p>
          </div>
          <div class="race-btns">
            <a class="black-btn" href="#">
              Edit
            </a>
            <a class="black-btn" href="#">
              Betting Window
            </a>
          </div>
        </div>
      </li>
      `;

      racesList.append(template);
      racesProcessed++;
      if (racesProcessed === races.length) toggleLoader();
    });
  });
}

function toggleLoader() {
  loader.css("display", "none");
}

function handleOnChange() {
  if (loading || invalidFields) return;

  const requestURL = `http://localhost/api/events?e=${params.get("e")}`;

  const data = {
    name: nameField.val(),
    date: dateField.val(),
    pot: Number.parseFloat(potField.val()),
  };

  $.ajax({
    type: "PUT",
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(data),
  }).done((data) => {
    eventNameHeader.text(nameField.val());
    console.log(data);
  });
}

function restrictNumberRange() {
  let value = parseInt(potField.val());
  let min = parseFloat(potField.attr("min"));
  let max = parseFloat(potField.attr("max"));

  if (value < min || value > max) {
    invalidFields = true;
    potField.addClass("error");
    return;
  }

  invalidFields = false;
  potField.removeClass("error");
}

$(document).ready(fetchEvent);

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);

potField.on("keyup", restrictNumberRange);
