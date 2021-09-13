// Extend jQuery to allow inserting a list item at specific index
jQuery.fn.insertAt = function (index, element) {
  let lastIndex = this.children().length;
  if (index < 0) {
    index = Math.max(0, lastIndex + 1 + index);
  }
  this.append(element);
  if (index < lastIndex) {
    this.children().eq(index).before(this.children().last());
  }
  return this;
};

// Extend String class to allow capitalizing the first letter of a string.
String.prototype.capitalize = function () {
  return this.charAt(0).toUpperCase() + this.slice(1);
};

// Used to get query string paramaters
const params = new URLSearchParams(window.location.search);

// Page state
const state = {
  loading: true,
  invalidFields: false,
  raceList: [],
  resultStatuses: [],
  eventName: "",
  currentRacePage: 1,
  lastRacePage: 1,
};

// Orchestration Function
async function fetchEvent() {
  displayEventInformation(0);
  await fetchEventRaceResultStatuses();
  fetchEventRaces();
  state.loading = false;
}

// Request Functions
// This one is async because other fcns depend on the state it sets
async function fetchEventRaceResultStatuses() {
  const requestURL = `/api/results/bulk?e=${params.get("e")}`;
  await $.get(requestURL, (data) => {
    state.resultStatuses = data.data;
  });
}

function fetchEventRaces() {
  const requestURL = `/api/races?e=${params.get("e")}&pg=${
    state.currentRacePage
  }`;
  $.get(requestURL, (data) => {
    state.lastRacePage = data.data.numberOfPages;
    const races = data.data.races;
    if (races.length === 0) {
      toggleLoader(false);
      toggleAddRace(0);
    } else {
      displayRacesUI(races);
      toggleNextAndPrevBtns();
    }
  });
}

function displayEventInformation(offset) {
  const pg = params.get("pg") ? params.get("pg") : "1";
  const reqPage = parseInt(pg) + offset;
  const requestURL = `/api/events?e=${params.get("e")}&pg=${reqPage}`;
  $.get(requestURL, (data) => {
    let event = data.data.events.filter(
      (event) => event.id == params.get("e")
    )[0];
    if (!event) {
      displayEventInformation(offset + 1);
      return;
    }
    state.eventName = event.name;
    updateEventInfoUI(
      event.name,
      Number.parseFloat(event.pot).toFixed(2),
      event.date
    );
  });
}

function handleEventChange() {
  if (state.loading || state.invalidFields) return;

  const data = {
    name: nameField.val(),
    date: dateField.val(),
    pot: Number.parseFloat(potField.val()).toFixed(2),
  };

  if (data.pot > 9999.99 || data.pot < 1) {
    restrictPot();
    return;
  }

  const requestURL = `/api/events?e=${params.get("e")}`;

  $.ajax({
    type: "PUT",
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(data),
    success: function (data) {
      const style = data.success === true ? "alert-success" : "alert-warning";
      $("#alert span#msg").text(data.messages[0]);
      $("#alert").removeClass("d-none").addClass(style);
    },
  }).done(() => {
    eventNameHeader.text(nameField.val());
  });
}

function updateRace(race, action) {
  if (action === "close") race.window_closed = 1;
  else if (action === "open") race.window_closed = 0;
  else return;

  toggleLoader(true);

  const requestURL = `/api/races/?e=${params.get("e")}&r=${race.race_number}`;
  $.ajax({
    type: "PUT",
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(race),
  }).done((data) => {
    const updatedRace = data.data[0];
    if (data.statusCode === 200) updateRaceUI(updatedRace);
  });
}

// UI Modifying Functions
function displayRacesUI(races) {
  let racesProcessed = 0;
  const racesList = $("#races-list");
  racesList.empty();
  races.forEach((race) => {
    const template = buildRaceTemplate(race);
    racesList.append(template);
    state.raceList.push(race);

    addBettingWindowListener(race);

    racesProcessed++;
    if (racesProcessed === races.length) {
      toggleLoader(false);
      toggleAddRace(racesProcessed);
    }
  });
}

function updateEventInfoUI(name, pot, date) {
  nameField.val(name);
  potField.val(pot);
  dateField.val(date);
}

function updateRaceUI(race) {
  $(`#${race.race_number}`).remove();
  $("#races-list").insertAt(race.race_number - 1, buildRaceTemplate(race));
  addBettingWindowListener(race);
  toggleLoader(false);
}

// Utility Functions
function fetchRaceById(raceId) {
  return state.raceList.filter((race) => race.race_number === raceId)[0];
}

function restrictPot() {
  let value = parseFloat(potField.val()).toFixed(2);
  let min = 1;
  let max = 9999.99;

  if (isNaN(value) || value < min || value > max) {
    state.invalidFields = true;
    potField.addClass("error");
    return;
  }

  state.invalidFields = false;
  potField.removeClass("error");
}

function changeBettingWindow(e) {
  if (state.loading) return;
  const raceInfo = e.target.id.split("-");
  const action = raceInfo[0];
  const raceId = Number.parseInt(raceInfo[1]);
  const race = fetchRaceById(raceId);
  updateRace(race, action);
}

function addBettingWindowListener(race) {
  const prefix = race.window_closed === 0 ? "close" : "open";
  const windowBtn = $(`#${prefix}-${race.race_number}`);
  windowBtn.on("click", changeBettingWindow);
}

function buildRaceTemplate(race) {
  const raceNumber = race.race_number;
  const sharedParams = `e=${params.get("e")}&r=${
    race.race_number
  }&pg=${params.get("pg")}&name=${state.eventName}`;
  const editRaceURL = `../races/race.php?${sharedParams}&mode=edit`;
  const enterResultsURL = `../races/results.php?${sharedParams}`;
  const resultsEntered = state.resultStatuses[raceNumber];

  const bettingWindowAction = race.window_closed == 0 ? "close" : "open";
  const bettingWindowButton = `
  <a class="black-btn btn ${
    resultsEntered ? "disabled" : ""
  }" id="${bettingWindowAction}-${race.race_number}">
    ${bettingWindowAction.capitalize()} Betting Window
  </a>`;

  const enterResultsButton = `<a class="black-btn outlined-btn btn" href="${enterResultsURL}">Enter Results</a>`;

  return `
  <li class="list-group-item" id="${race.race_number}">
    <div class="flex-space-between">
      <div class="event-title-container"> 
        <p class="event-title">
          Race ${race.race_number}
        </p>
      </div>
      <div class="race-btns" id="btns-${race.race_number}">
        ${race.window_closed == 1 ? enterResultsButton : ""}
        <a class="black-btn btn ${
          resultsEntered ? "disabled" : ""
        }"" href="${editRaceURL}">
          Edit
        </a>
        ${bettingWindowButton}
      </div>
    </div>
  </li>
  `;
}

// UI Toggle Functions
function toggleAddRace(numRaces) {
  $("#add-race-container").css("display", "block");
  addRaceButton.attr(
    "href",
    `../races/race.php?e=${params.get("e")}&r=${numRaces + 1}&pg=${params.get(
      "pg"
    )}&mode=create`
  );
}

function toggleLoader(show) {
  if (show) {
    loader.css("display", "flex");
    $("#race-loader").css("display", "block");
  } else {
    loader.css("display", "none");
    $("#race-loader").css("display", "none");
  }
}

function toggleNextAndPrevBtns() {
  if (state.lastRacePage > state.currentRacePage) {
    nextBtn.attr("style", "display: block;");
  } else {
    nextBtn.attr("style", "display: none;");
  }

  if (state.currentRacePage > 1) {
    prevBtn.attr("style", "display: block;");
  } else {
    prevBtn.attr("style", "display: none;");
  }
}

function togglePage(change) {
  state.currentRacePage += change;
  toggleLoader(true);
  fetchEventRaces();
  toggleLoader(false);
}

// DOM Elements
const eventNameHeader = $("#event-name");
const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const loader = $("#loader-container");
const addRaceButton = $("#add-race-container p a");
const nextBtn = $("#next-btn");
const prevBtn = $("#prev-btn");

// Event Listeners
$(document).ready(fetchEvent);
nameField.on("change", handleEventChange);
dateField.on("change", handleEventChange);
potField.on("change", handleEventChange);
potField.on("keyup", restrictPot);

nextBtn.on("click", () => togglePage(1));
prevBtn.on("click", () => togglePage(-1));
