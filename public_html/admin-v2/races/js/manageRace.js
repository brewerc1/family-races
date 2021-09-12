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
  mode: params.get("mode"),
};

// Page load orchestration function
async function preparePage() {
  if (!state.page || state.page === "null") state.page = 1;
  displayStateInformationUI();
  await fetchEvent();
  displayEventUI();
  if (state.mode === "edit") {
    await fetchRaceHorses();
    displayExistingHorsesUI();
  }
  toggleLoader();
}

// Orchestrates requests when race is saved
async function orchestrateRequests(e, isUpdate) {
  if (params.get("mode") === "create") await createRace();
  if (state.horsesToDelete.length > 0) await deleteHorses();
  if (isUpdate) {
    const horseNotCreated = $(`#${e.target.id}`).hasClass("not-created");
    if (horseNotCreated) await createNewHorse(e.target.id);
    else await updateHorse(e.target.id);
  }
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

async function createRace() {
  const requestURL = `/api/races/`;
  const horses = [];

  $("#horses .horse input").each((i, elem) => {
    const name = $(elem).val();
    if (horseHasName(name)) horses.push($(elem).val());
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
  if (request.status !== 201) console.log("Error creating race.");
}

async function updateHorse(id) {
  const element = $(`#${id}`);

  if (!validateHorse(element)) return;

  const newName = element.val();
  const idStartIdx = id.indexOf("e");
  const idEndIdx = id.indexOf("-");
  const horseId = id.substring(idStartIdx + 1, idEndIdx);
  const requestURL = "/api/horses";
  const data = {
    horses: [
      {
        id: horseId,
        horse_number: newName,
      },
    ],
  };

  const request = await fetch(requestURL, {
    method: "PUT",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
    },
  });
  await request.json();

  if (request.status !== 200) console.log("Error updating horse.");
}

async function deleteHorses() {
  const requestURL = `/api/horses`;

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

async function createNewHorse(oldID) {
  const name = $(`#${oldID}`).val();
  const requestURL = "/api/horses/";

  const data = {
    race_event_id: state.eventId,
    race_race_number: state.race,
    horses: [name],
  };

  const request = await fetch(requestURL, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
    },
  });
  const response = await request.json();

  if (request.status !== 201) console.log("Error creating horse.");
  else updateCreatedHorseUI(response.data[response.data.length - 1].id, oldID);
}

// UI Modifying Functions
function displayEventUI() {
  const eventURL = `../events/manage.php?e=${state.eventId}&pg=${state.page}`;
  $("#event-name").attr("href", eventURL);
}

function displayStateInformationUI() {
  if (state.mode === "create") $(".checkbox-container").css("display", "none");
}

function displayExistingHorsesUI() {
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
  const template = buildNewHorseTemplateUI();
  $("#horses").append(template);
  $(`#delete-horse${state.numHorses}`).on("click", () =>
    deleteHorseUI(state.numHorses)
  );
}

function deleteHorseUI(horse) {
  $(`#horse${horse}`).remove();
  if (state.numHorses === 0) $("#remove-hint").css("display", "none");
  if (state.mode === "edit") state.horsesToDelete.push({ id: horse });
  state.horses = state.horses.filter((horse) => horse.id !== horse);
  state.numHorses--;
}

function updateCreatedHorseUI(id, oldId) {
  const element = $(`#${oldId}`);
  element.siblings().attr("id", `delete-horse${id}`);
  element.parent().attr("id", `horse${id}`);
  element.removeClass("not-created");
  element.attr("id", `horse${id}-name`);
  $(`#delete-horse${id}`).on("click", () => deleteHorseUI(id));
}

function addHorseErrorUI(element) {
  state.numErrors++;
  const changedHorseId = element.attr("id").split("e")[1].split("-")[0];
  element.addClass("is-invalid");
  $("#race-done").addClass("disabled");
  $(`#delete-horse${changedHorseId}`).addClass("disabled");
}

function removeHorseErrorUI(element) {
  state.numErrors--;
  const changedHorseId = element.attr("id").split("e")[1].split("-")[0];
  element.removeClass("is-invalid");
  $(`#delete-horse${changedHorseId}`).removeClass("disabled");
  if (state.numErrors === 0) $("#race-done").removeClass("disabled");
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

function buildNewHorseTemplateUI() {
  return `
  <div class="horse" id="horse${state.numHorses}">
    <input type="text" class="form-control not-created" placeholder="Name of horse"  id="horse${state.numHorses}-name">
    <div class="black-btn" id="delete-horse${state.numHorses}"><i class="fas fa-minus-circle"></i>Delete</div>
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
  }
}

function toggleLoader() {
  $("#race-loader").css("display", "none");
  state.loading = false;
}

// Event Listeners
$(document).ready(preparePage);
$("#add-horse-container p a").on("click", createHorseUI);
$("#race-done").on("click", (e) => orchestrateRequests(e, (isUpdate = false)));
if (params.get("mode") === "edit") {
  $("#horses").on("change", (e) => orchestrateRequests(e, (isUpdate = true)));
}
