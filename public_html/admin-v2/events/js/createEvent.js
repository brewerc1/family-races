const state = { eventCreated: false, eventId: null };

const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const saveEventBtn = $("#save-event");

function createEvent() {
  const event = {
    name: nameField.val(),
    date: dateField.val(),
    pot: Number.parseFloat(potField.val()),
  };

  let requestURL = `http://localhost/api/events/`;

  console.log(state);

  if (state.eventCreated) requestURL = requestURL.concat(`?e=${state.eventId}`);

  const requestType = state.eventCreated ? "PUT" : "POST";

  $.ajax({
    type: requestType,
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(event),
  }).done((data) => {
    console.log(data);
    if (!state.eventCreated) state.eventId = data.data[0].id;
    state.eventCreated = true;
  });
}

function handleOnChange() {
  let allFieldsComplete = nameField.val() && dateField.val() && potField.val();

  if (!allFieldsComplete) return;

  createEvent();
}

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);

saveEventBtn.on("click", (e) => {
  e.preventDefault();
});

$(document).ready();
