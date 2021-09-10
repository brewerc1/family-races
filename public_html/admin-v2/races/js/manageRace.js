const params = new URLSearchParams(window.location.search);
const state = {
  event: null,
  numHorses: 0,
  loading: true,
  eventId: params.get("e"),
  page: params.get("pg"),
  race: params.get("r"),
  mode: params.get("mode"),
};

let horsesToDelete = [];

function displayInformation() {
  fetchEvent();

  $("#race-mode").text(
    state.mode === "create" ? "Create a Race" : "Edit a Race"
  );

  if (state.mode === "create") $(".checkbox-container").css("display", "none");

  if (state.mode === "edit") {
    fetchRaceHorses();
    let toolTipNeeded = $("highlight-race").hasClass("disabled");
    if (toolTipNeeded) $("#highlight-race").toolTip();
  }
}

function fetchEvent() {
  const requestURL = `http://localhost/api/events?e=${state.eventId}&pg=${state.page}`;

  $.get(requestURL, (data) => {
    let event = data.data.events.filter(
      (event) => event.id == state.eventId
    )[0];

    state.event = event;

    $("#event-name").text(event.name);
    $("#event-name").attr(
      "href",
      `../events/manage.php?e=${state.eventId}&pg=${state.page}`
    );
    $("#race-loader").css("display", "none");
  });
}

function fetchRaceHorses() {
  const requestURL = `http://localhost/api/horses?e=${state.eventId}&r=${state.race}`;

  $.get(requestURL).done((data) => {
    const horses = data.data;
    state.numHorses = horses.length;

    if (state.numHorses >= 1) $("#remove-hint").css("display", "block");

    horses.forEach((horse) => {
      const deleteStatus = horse.can_be_deleted ? "" : "disabled";
      const template = `
      <div class="horse" id="horse${horse.id}">
			  <input type="text" class="form-control" placeholder="Name of horse" id="horse${horse.id}-name"
        value="${horse.horse_number}">
			  <a class="black-btn btn ${deleteStatus}"
        id="delete-horse${horse.id}"><i class="fas fa-minus-circle">
        </i>Delete</a>
	    </div>`;

      $("#horses").append(template);
      addListeners(horse.id);
    });
  });
}

function addHorse() {
  state.numHorses++;

  if (state.numHorses === 1) $("#remove-hint").css("display", "block");

  const template = `
    <div class="horse" id="horse${state.numHorses}">
			<input type="text" class="form-control not-created" placeholder="Name of horse"  id="horse${state.numHorses}-name">
			<div class="black-btn" id="delete-horse${state.numHorses}"><i class="fas fa-minus-circle"></i>Delete</div>
		</div>`;

  $("#horses").append(template);

  addListeners(state.numHorses);
}

function addListeners(horse) {
  $(`#delete-horse${horse}`).on("click", (e) => deleteHorse(horse, e));
}

function deleteHorse(horse, e) {
  e.stopPropagation();

  $(`#horse${horse}`).remove();
  state.numHorses--;

  if (state.numHorses === 0) $("#remove-hint").css("display", "none");

  if (state.mode === "edit") horsesToDelete.push({ id: horse });
}

function updateRace(e, update) {
  if (params.get("mode") === "create") {
    const requestURL = `http://localhost/api/races/`;
    const horses = [];

    $("#horses .horse input").each((index, elem) => {
      const name = $(elem).val();
      if (hasName(name)) horses.push(name);
    });

    const data = {
      event_id: state.eventId,
      horses: horses,
    };

    $.ajax({
      type: "POST",
      url: requestURL,
      contentType: "application/json",
      data: JSON.stringify(data),
    });

    return;
  }

  if (update) {
    const element = $(`#${e.target.id}`);

    const notCreated = element.hasClass("not-created");

    if (notCreated) {
      const name = element.val();

      if (!name) return;

      const createHorseURL = "http://localhost/api/horses/";

      const createHorseData = {
        race_event_id: state.eventId,
        race_race_number: state.race,
        horses: [name],
      };

      $.ajax({
        type: "POST",
        url: createHorseURL,
        contentType: "application/json",
        data: JSON.stringify(createHorseData),
      }).done((data) => {
        const newId = data.data[data.data.length - 1].id;
        element.siblings().attr("id", `delete-horse${newId}`);
        element.parent().attr("id", `horse${newId}`);
        element.removeClass("not-created");
        element.attr("id", `horse${newId}-name`);
        addListeners(newId);
      });
    } else {
      // Horse already created, update horse
      const newName = element.val();
      // Hacky way to get the affected horses ID
      const changedHorseId = element.attr("id").split("e")[1].split("-")[0];
      // Was the horse given a name?
      if (!hasName(newName)) {
        element.addClass("is-invalid");
        $("#race-done").addClass("disabled");
        $(`#delete-horse${changedHorseId}`).addClass("disabled");
        return;
      } else {
        element.removeClass("is-invalid");
        $("#race-done").removeClass("disabled");
        $(`#delete-horse${changedHorseId}`).removeClass("disabled");
      }

      const id = element.attr("id");

      let idStartIdx = id.indexOf("e");
      let idEndIdx = id.indexOf("-");
      const horseId = id.substring(idStartIdx + 1, idEndIdx);
      const updateHorseURL = "http://localhost/api/horses";

      const updateHorseData = {
        horses: [
          {
            id: horseId,
            horse_number: newName,
          },
        ],
      };

      $.ajax({
        type: "PUT",
        url: updateHorseURL,
        contentType: "application/json",
        data: JSON.stringify(updateHorseData),
      });
    }
  }

  const deleteRequestURL = `http://localhost/api/horses`;

  const deleteData = { horses: horsesToDelete };

  $.ajax({
    url: deleteRequestURL,
    type: "DELETE",
    contentType: "application/json",
    data: JSON.stringify(deleteData),
  });
}

// Was the horse given a name?
function hasName(name) {
  const withoutSpaces = name.replace(/ /g, "");
  if (!withoutSpaces) return false;
  return true;
}

$(document).ready(displayInformation);

$("#add-horse-container p a").on("click", addHorse);

$("#race-done").on("click", (e) => updateRace(e, false));

if (params.get("mode") === "edit") {
  $("#horses").on("change", (e) => updateRace(e, true));
}
