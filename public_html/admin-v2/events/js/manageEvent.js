const params = new URLSearchParams(window.location.search);

let loading = true;

const eventNameHeader = $("#event-name");
const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const loader = $("#loader-container");

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
  let racesProcessed = 0;
  const racesList = $("#races-list");
  const url = `http://localhost/api/races?e=${params.get("e")}`;
  $.get(url, (data) => {
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
