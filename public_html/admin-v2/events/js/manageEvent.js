const state = { currentEvent: null };

// $("#manage-events-page-header").text(`Events > ${currentEvent.name}`);

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
  const id = queryStringParams.e;

  // Fetch this event
  const requestURL = `http://localhost/api/events?e=${id}`;
  $.get(requestURL, (data) => {
    console.log(data);
  });

  // Display error if none
}

$(document).ready(fetchEvent);
