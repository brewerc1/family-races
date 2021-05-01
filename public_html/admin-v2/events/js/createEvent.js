const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const saveEventBtn = $("#save-event");

function createEvent() {
  console.log("creating event");

  const event = {
    event_name: nameField.val(),
    date: dateField.val(),
    pot: potField.val(),
  };

  console.log(event);
}

// Checks for errors
function handleOnChange() {
  console.log("handling change");

  let allFieldsComplete = nameField.val() && dateField.val() && potField.val();

  if (!allFieldsComplete) return;

  createEvent();
}

nameField.on("change", handleOnChange);
dateField.on("change", handleOnChange);
potField.on("change", handleOnChange);

saveEventBtn.on("click", (e) => {
  e.preventDefault();
  createEvent();
});
