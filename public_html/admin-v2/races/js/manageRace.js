// URL query string parameters
const params = new URLSearchParams(window.location.search);

// Page state
const state = {
  event: null,
  numHorses: 0,
  horsesToDelete: [],
  horses: [],
  loading: true,
  numErrors: 0,
  eventId: params.get("e"),
  page: params.get("pg"),
  race: params.get("r"),
  mode: params.get("mode").toLowerCase(),
};

// Page load orchestration function
async function preparePage() {
  if (!state.page || state.page === "null") state.page = 1;
  displayStateInformationUI();
  await fetchEvent();
  displayEventUI();
  if (state.mode === "edit") {
    await fetchRaceHorses();
    displayHorsesUI();
  }
  toggleLoader(false);
}

// Orchestrates requests when race is saved
async function orchestrateRequests(e) {
  if (state.mode !== "create") e.preventDefault();
  else e.preventDefault();
  toggleLoader(true);
  if (state.mode === "create") await createRace();
  else await updateRace();
  toggleLoader(false);
}

// Orchestrates race updating
async function updateRace() {
  const existingHorses = [];
  const newHorses = [];
  let errorsExist = false;
  $("#horses .horse input").each((i, elem) => {
    const element = $(elem);
    const name = element.val();
    if (!horseHasName(name)) {
      addHorseErrorUI(element);
      errorsExist = true;
    } else removeHorseErrorUI(element);
    const horse = {
      id: getHorseId(element),
      horse_number: getHorseName(element),
    };
    if (element.hasClass("not-created")) newHorses.push(horse);
    else existingHorses.push(horse);
  });
  if (errorsExist) return;
  await deleteHorses();
  await updateExistingHorses(existingHorses);
  await createNewHorses(newHorses);
}

// Request Functions
async function fetchEvent() {
  const requestURL = `/api/events?e=${state.eventId}&pg=${state.page}`;
  const request = await fetch(requestURL);
  const response = await request.json();
  const events = response.data.events;
  const event = events.filter((event) => event.id == state.eventId)[0];
  state.event = event;
}

async function fetchRaceHorses() {
  const requestURL = `/api/horses?e=${state.eventId}&r=${state.race}`;
  const request = await fetch(requestURL);
  const response = await request.json();
  const horses = response.data;
  state.horses = response.data;
  state.numHorses = horses.length;
}

async function updateExistingHorses(horses) {
  const requestURL = "/api/horses/";

  const data = {
    horses: horses,
  };

  const request = await fetch(requestURL, {
    method: "PUT",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
    },
  });

  await request.json();
  if (request.status !== 200) alert("Error updating horse.");
}

async function createNewHorses(horses) {
  const requestURL = "/api/horses/";

  const newHorseNames = horses.map((horse) => horse.horse_number);

  const data = {
    race_event_id: state.eventId,
    race_race_number: state.race,
    horses: newHorseNames,
  };

  const request = await fetch(requestURL, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (request.status !== 201) {
    return;
  }

  const response = await request.json();
  state.horses = response.data;
  displayHorsesUI();
}

async function createRace() {
  const requestURL = `/api/races/`;
  const horses = [];

  $("#horses .horse input").each((i, elem) => {
    const name = $(elem).val();
    if (horseHasName(name)) horses.push(getHorseName($(elem)));
  });

  const data = {
    event_id: state.eventId,
    horses: horses,
  };

  const request = await fetch(requestURL, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
    },
  });
  await request.json();
  if (request.status !== 201) alert("Error creating race.");
  else {
    const managePage = `../events/manage.php?e=${state.eventId}&pg=${state.page}`;
    window.location.href = managePage;
  }
}

async function deleteHorses() {
  const requestURL = `/api/horses/`;

  const data = { horses: state.horsesToDelete };

  const request = await fetch(requestURL, {
    method: "DELETE",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (request.status !== 204) console.log("Error deleting horse.");
  else state.horsesToDelete = [];
}

// UI Modifying Functions
function displayEventUI() {
  const eventURL = `../events/manage.php?e=${state.eventId}&pg=${state.page}`;
  $("#event-name").attr("href", eventURL);
}

function displayStateInformationUI() {
  if (state.mode === "create") $(".checkbox-container").css("display", "none");
}

function displayHorsesUI() {
  $("#horses").empty();
  if (state.numHorses >= 1) $("#remove-hint").css("display", "block");
  state.horses.forEach((horse) => {
    const template = buildExistingHorseTemplateUI(horse);
    $("#horses").append(template);
    $(`#delete-horse${horse.id}`).on("click", () => deleteHorseUI(horse.id));
  });
}

function createHorseUI() {
  state.numHorses++;
  if (state.numHorses === 1) $("#remove-hint").css("display", "block");
  const randomId = Math.floor(100000 + Math.random() * 100000);
  const template = buildNewHorseTemplateUI(randomId);
  $("#horses").append(template);
  $(`#delete-horse${randomId}`).on("click", () => deleteHorseUI(randomId));
}

function deleteHorseUI(horse) {
  const horseCreated = !$(`#horse${horse} input`).hasClass("not-created");
  $(`#horse${horse}`).remove();
  if (state.numHorses === 0) $("#remove-hint").css("display", "none");
  if (state.mode === "edit" && horseCreated) {
    state.horsesToDelete.push({ id: horse });
  }
  state.horses = state.horses.filter((horse) => horse.id !== horse);
  state.numHorses--;
}

function addHorseErrorUI(element) {
  const changedHorseId = element.attr("id").split("e")[1].split("-")[0];
  element.addClass("is-invalid");
  $(`#delete-horse${changedHorseId}`).addClass("disabled");
}

function removeHorseErrorUI(element) {
  const changedHorseId = getHorseId(element);
  element.removeClass("is-invalid");
  $(`#delete-horse${changedHorseId}`).removeClass("disabled");
}

// UI Template Functions
function buildExistingHorseTemplateUI(horse) {
  const deleteStatus = horse.can_be_deleted ? "" : "disabled";
  return `
  <div class="horse" id="horse${horse.id}">
    <input type="text" class="form-control" placeholder="Name of horse" id="horse${horse.id}-name"
    value="${horse.horse_number}">
    <a class="black-btn btn ${deleteStatus}"
      id="delete-horse${horse.id}"><i class="fas fa-minus-circle">
    </i>Delete</a>
  </div>`;
}

function buildNewHorseTemplateUI(id) {
  return `
  <div class="horse" id="horse${id}">
    <input type="text" class="form-control not-created" placeholder="Name of horse"  id="horse${id}-name">
    <div class="black-btn" id="delete-horse${id}"><i class="fas fa-minus-circle"></i>Delete</div>
  </div>`;
}

// Utility Functions
function horseHasName(name) {
  const withoutSpaces = name.replace(/ /g, "");
  if (!withoutSpaces) return false;
  return true;
}

function validateHorse(element) {
  const name = element.val();
  if (!horseHasName(name)) {
    addHorseErrorUI(element);
    return false;
  } else {
    removeHorseErrorUI(element);
    return true;
  }
}

function getHorseId(element) {
  return element.attr("id").split("e")[1].split("-")[0];
}

function getHorseName(element) {
  return element.val().length < 48
    ? element.val()
    : element.val().substring(0, 48);
}

function toggleLoader(show) {
  if (show) {
    $("#race-loader").css("display", "block");
    state.loading = true;
  } else {
    $("#race-loader").css("display", "none");
    state.loading = false;
  }
}

// Event Listeners
$(document).ready(preparePage);
$("#add-horse-container p a").on("click", createHorseUI);
$("#race-done").on("click", (e) => orchestrateRequests(e));
