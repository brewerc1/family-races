const highlightBtn = $("#highlight-race");
const urlParams = new URLSearchParams(window.location.search);

function highlightRace() {
  const checked = highlightBtn.is(":checked");
  if (!checked) return;

  const data = { race: urlParams.get("r") };
  const requestURL = "/admin-v2/races/race.php";
  $.ajax({
    type: "POST",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    dataType: "json",
    url: requestURL,
    data,
  });
}

highlightBtn.click(highlightRace);
