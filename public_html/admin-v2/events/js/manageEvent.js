const params = new URLSearchParams(window.location.search);

let loading = true;
let invalidFields = false;
let racesProcessed = 0;

const eventNameHeader = $("#event-name");
const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const loader = $("#loader-container");

const addRaceButton = $("#add-race-container p a");

function fetchEvent() {
  displayEventInformation();
  displayEventRaces();
  loading = false;
}

function displayEventInformation() {
  // Need page due to API
  const requestURL = `/api/events?e=${params.get(
    "e"
  )}`;
  $.get(requestURL, (data) => {
    // Hacky, only way this can be done with the current API
    let event = data.data.events.filter(
      (event) => event.id == params.get("e")
    )[0];

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
  const racesList = $("#races-list");
  const requestURL = `/api/races?e=${params.get("e")}`;
  $.get(requestURL, (data) => {
    const races = data.data.races;

    if (races.length === 0) {
      toggleLoader();
      toggleAddRace(0);
      return;
    }

    races.forEach((race) => {
      const editRaceURL = `../races/race.php?e=${params.get("e")}&r=${
        race.race_number
      }&pg=${params.get("pg")}&mode=edit`;

      const template = `
      <li class="list-group-item" id="${race.race_number}">
        <div class="flex-space-between">

          <div class="event-title-container"> 
            <p class="event-title">
              Race ${race.race_number}
            </p>
          </div>
          <div class="race-btns">
            <a class="black-btn" href="${editRaceURL}">
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
      if (racesProcessed === races.length) {
        toggleLoader();
        toggleAddRace(racesProcessed);
      }
    });
  });
}

function toggleAddRace(numRaces) {
  $("#add-race-container").css("display", "block");
  addRaceButton.attr(
    "href",
    `../races/race.php?e=${params.get("e")}&r=${numRaces + 1}&pg=${params.get(
      "pg"
    )}&mode=create`
  );
}

function toggleLoader() {
  loader.css("display", "none");
}

function handleOnChange() {
  if (loading || invalidFields) return;

  const requestURL = `/api/events?e=${params.get("e")}`;

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
    success: function (data) {
      console.log(data);
      const style = data.success === true ? "alert-success" : "alert-warning";
      $("#alert span#msg").text(data.messages[0]);
      $("#alert").removeClass("d-none").addClass(style);
    }
  }).done(() => {
    eventNameHeader.text(nameField.val());
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
