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

  let requestURL = `/api/events/`;

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
    saveEventBtn.removeClass("disabled");
    //saveEventBtn.attr("href", `./manage.php?e=${state.eventId}`); //change
    window.location.href = `./manage.php?e=${state.eventId}`;
  });
}

function handleOnChange() {
  let allFieldsComplete = nameField.val() && dateField.val() && potField.val();
  // TODO: Simplify below
  if (!allFieldsComplete) saveEventBtn.attr("class", "btn btn-primary btn col-sm-5 disabled");
  else saveEventBtn.attr("class", "btn btn-primary btn col-sm-5");
}

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);
saveEventBtn.on("click", createEvent);
