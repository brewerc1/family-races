const params = new URLSearchParams(window.location.search);
const state = { event: null, numHorses: 0, mode: "create" };

function displayInformation() {
  fetchEvent();
  if (params.get("mode") === "create") {
    $("#race-mode").text("Create a Race");
  }
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
      `../events/?e=${params.get("e")}&pg=${params.get("pg")}`
    );
  });
}

function addHorse() {
  state.numHorses++;

  if (state.numHorses === 1) $("#remove-hint").css("display", "block");

  const template = `
        <div class="horse" id="horse${state.numHorses}">
			<input type="text" class="form-control" placeholder="Name of horse"  id="horse${state.numHorses}-name">
			<div class="black-btn" id="delete-horse${state.numHorses}"><i class="fas fa-minus-circle"></i>Delete</div>
		</div>`;
  $("#horses").append(template);

  addDelete(state.numHorses);
}

function addDelete(horse) {
  $(`#delete-horse${horse}`).on("click", (e) => deleteHorse(horse, e));
}

function deleteHorse(horse, e) {
  e.stopPropagation(); // stops event bubbling
  $(`#horse${horse}`).remove();
  state.numHorses--;
  if (state.numHorses === 0) $("#remove-hint").css("display", "none");
}

function updateRace() {
  if (params.get("mode") === "create") {
    const requestURL = `http://localhost/api/races/`;

    const horses = [];

    $("#horses .horse input").each((index, elem) => horses.push($(elem).val()));

    console.log(horses);

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

  // Update race
}

$(document).ready(displayInformation);

$("#add-horse-container p a").on("click", addHorse);

$("#race-done").on("click", () => updateRace());
