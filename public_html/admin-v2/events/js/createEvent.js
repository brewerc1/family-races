const nameField = $("#name");
const dateField = $("#date");
const potField = $("#pot");
const saveEventBtn = $("#save-event");

function createEvent() {
  console.log("creating event");

  const event = {
    name: nameField.val(),
    date: dateField.val(),
    pot: potField.val(),
  };

  const requestURL = `http://localhost/api/events/`;

  // $.post(requestURL, JSON.stringify(event), (data) => {
  //   console.log(data);
  // });

  $.ajax({
    type: "POST",
    url: requestURL,
    contentType: "application/json",
    data: JSON.stringify(event),
  }).done(() => console.log("done"));
}

// Checks for errors, will need put method if it is done this way
// function handleOnChange() {
//   console.log("handling change");

//   let allFieldsComplete = nameField.val() && dateField.val() && potField.val();

//   if (!allFieldsComplete) return;

//   createEvent();
// }

// nameField.on("change", handleOnChange);
// dateField.on("change", handleOnChange);
// potField.on("change", handleOnChange);

saveEventBtn.on("click", (e) => {
  e.preventDefault();
  createEvent();
});
