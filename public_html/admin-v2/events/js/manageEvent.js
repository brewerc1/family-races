const state = { currentEvent: null };

function getUrlVars() {
  const queries = {};
  $.each(document.location.search.substr(1).split("&"), function (c, q) {
    let i = q.split("=");
    queries[i[0].toString()] = i[1].toString();
  });

  return queries;
}

function fetchEvent() {
  // Get query string
  const queryStringParams = getUrlVars();
  state.currentEvent = queryStringParams;

  // Fetch this events races
  const requestURL = `http://localhost/api/races?e=${state.currentEvent.e}`;
  $.get(requestURL, (data) => {
    console.log(data);
  });

  // Display error if none
}

$(document).ready(fetchEvent);
