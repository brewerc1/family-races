const state = { eventCreated: false, eventId: null };

const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const saveEventBtn = $("#save-event");

function createEvent() {
  let validPot = potField.val() <= 99999 && potField.val() >= 1;

  // Extra guard to prevent large pots
  if (!validPot) {
    handleOnChange();
    return;
  }

  const event = {
    name: nameField.val(),
    date: dateField.val(),
    pot: Number.parseFloat(potField.val()),
  };

  let requestURL = `/api/events/`;

  if (state.eventCreated) requestURL = requestURL.concat(`?e=${state.eventId}`);

  const requestType = state.eventCreated ? "PUT" : "POST";

  $.ajax({
    type: requestType,
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(event),
  }).done((data) => {
    if (!state.eventCreated) state.eventId = data.data[0].id;
    state.eventCreated = true;
    saveEventBtn.removeClass("disabled");
    window.location.href = `./manage.php?e=${state.eventId}`;
  });
}

function handleOnChange() {
  let allFieldsComplete = nameField.val() && dateField.val() && potField.val();
  let validPot = potField.val() <= 9999.99 && potField.val() >= 1;

  if (potField.val() && !validPot) potField.addClass("is-invalid");
  else potField.removeClass("is-invalid");

  if (!allFieldsComplete || !validPot)
    saveEventBtn.attr("class", "btn btn-primary btn col-sm-5 disabled");
  else saveEventBtn.attr("class", "btn btn-primary btn col-sm-5 text-white");
}

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);
saveEventBtn.on("click", createEvent);
