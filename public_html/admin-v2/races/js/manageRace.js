const params = new URLSearchParams(window.location.search);
const state = { event: null, numHorses: 0 };

let horsesToDelete = [];

function displayInformation() {
  fetchEvent();
  if (params.get("mode") === "create") {
    $("#race-mode").text("Create a Race");
    return;
  }
  $("#race-mode").text("Edit a Race");
  fetchRaceHorses();
}

function fetchEvent() {
  const requestURL = `http://localhost/api/events?e=${params.get(
    "e"
  )}&pg=${params.get("pg")}`;

  $.get(requestURL, (data) => {
    let event = data.data.events.filter(
      (event) => event.id == params.get("e")
    )[0];

    state.event = event;

    $("#event-name").text(event.name);
    $("#event-name").attr(
      "href",
      `../events/manage.php?e=${params.get("e")}&pg=${params.get("pg")}`
    );
  });
}

function fetchRaceHorses() {
  const requestURL = `http://localhost/api/horses?e=${params.get(
    "e"
  )}&r=${params.get("r")}`;

  $.get(requestURL).done((data) => {
    const horses = data.data;
    state.numHorses = horses.length;

    if (state.numHorses >= 1) $("#remove-hint").css("display", "block");

    horses.forEach((horse) => {
      const template = `
      <div class="horse" id="horse${horse.id}">
			  <input type="text" class="form-control" placeholder="Name of horse" id="horse${
          horse.id
        }-name"
        value="${horse.horse_number}">
			  <div class="black-btn ${horse.canBeDeleted ? "" : "disabled"}"
        id="delete-horse${horse.id}"><i class="fas fa-minus-circle">
        </i>Delete</div>
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
  e.stopPropagation(); // stops event bubbling
  $(`#horse${horse}`).remove();
  state.numHorses--;
  if (state.numHorses === 0) $("#remove-hint").css("display", "none");

  if (params.get("mode") === "edit") {
    horsesToDelete.push({ id: horse });
  }
}

function updateRace(e, update) {
  if (params.get("mode") === "create") {
    const requestURL = `http://localhost/api/races/`;

    const horses = [];

    $("#horses .horse input").each((index, elem) => horses.push($(elem).val()));

    const data = {
      event_id: params.get("e"),
      horses: horses,
    };

    $.ajax({
      type: "POST",
      url: requestURL,
      contentType: "application/json",
      data: JSON.stringify(data),
    }).done((data) => {
      console.log(data);
    });
    return;
  }

  if (update) {
    // Check if horse has been created, if not, create it, update ids
    const element = $(`#${e.target.id}`);

    const notCreated = element.hasClass("not-created");

    if (notCreated) {
      const name = element.val();

      const createHorseURL = "http://localhost/api/horses/";

      const createHorseData = {
        race_event_id: params.get("e"),
        race_race_number: params.get("r"),
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
      }).done((data) => {
        console.log(data);
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

$(document).ready(displayInformation);

$("#add-horse-container p a").on("click", addHorse);

$("#race-done").on("click", (e) => updateRace(e, false));

if (params.get("mode") === "edit") {
  $("#horses").on("change", (e) => updateRace(e, true));
}
