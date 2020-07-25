<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Manage an Event";
$javascript = "";

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}
$event_name = "Event Name";
$event_date = "Event Date";
$event_pot = 0;


$e = isset($_GET["e"]) ? $_GET["e"] : NULL;
$event_id = filter_var($e, FILTER_VALIDATE_INT) ? $e : 0;

if ($event_id == 0) {
    $javascript .= "
       $('#addRace').addClass('disabled');
    ";
} else {

    $query = "SELECT name, status, date, pot FROM event WHERE id = :id";
    $event = $pdo->prepare($query);
    $event->execute(['id' => $event_id]);

    if ($event->rowCount() > 0) {
        $row = $event->fetch();
        $event_name = $row["name"];
        $event_date = $row["date"];
        $event_status = $row["status"];
        $event_pot = intval(explode(".", $row["pot"])[0]);
    }
}
$debug = debug();
?>
{header}
{main_nav}
<script>
    let deleteHorsesIDS = [];
    let cancelIds = [];
    let racesIDs = [];
    let addHorseIDS = [];
    let selectIDS = [];
    let defaultHorseCount;
    let numberOfHorses;

    function changeGoToRaceButtonToUpdateRaceButton(raceNumber) {
        $('#update' + raceNumber).removeClass('d-none');
        $('#goToRace' + raceNumber).addClass('d-none');
    }
</script>
<main role="main">
    <section>
        <h1>Manage an Event</h1>

        <div class="text-center">
            <h2><?php echo $event_name ?></h2>
            <span><?php echo $event_date ?></span>
        </div>

        <form method="POST" class="mt-3">
            <div class="form-row">
                <div class="input-group mb-3">
                    <label class="col-sm-2 col-form-label"  for="pot">Jackpot</label>
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control" name="pot" id="pot" aria-label="Amount (to the nearest dollar)" value="<?php echo $event_pot ?>">
                    <div class="input-group-append">
                        <span class="input-group-text">.00</span>
                    </div>
                </div>
            </div>

            <fieldset class="accordion border border-dark" id="accordion01">
                <legend class="text-center w-auto">Races</legend>
                <?php


                $query = "SELECT race_number, window_closed, cancelled FROM race WHERE event_id = :event_id";
                $races = $pdo->prepare($query);
                if ($races->execute(['event_id' => $event_id])) {
                    if ($races->rowCount() > 0) {
                        $row = $races->fetchAll();
                        $index = 0;
                        while ($index < count($row)) {
                            $race_num = $row[$index]["race_number"];
                            $checked = "";
                            $displayCard = "";
                            $addHorse = "";
                            $canceled = "d-none";
                            if ($row[$index]["cancelled"]) {
                                $checked = "checked";
                                $displayCard = "d-none";
                                $canceled = "";
                                $addHorse = "disabled";
                            }

$race_HTML = <<< HTML
                <!--- Race HTML -->
                <div class="group border-bottom border-dark">
                    <button class="btn btn-block dropdown-toggle dt" type="button" data-toggle="collapse" data-target="#collapse$race_num" aria-expanded="true" aria-controls="collapseOne">
                        Race $race_num
                    </button>
                    <div id="collapse$race_num" class="collapse race" data-parent="#accordion01">
                        <div class="card-body">
                            <div class="text-center $canceled" id="c$race_num">
                                Race $race_num has been <span class="badge badge-danger">Canceled</span>
                            </div>
                            <div class="$displayCard" id="card$race_num">
                                <div class="d-flex flex-row-reverse mb-2"><a href="#" id="deleteRace$race_num" class="btn btn-outline-danger">Delete Race $race_num</a></div>
                                <div class="form-row">
                                    <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                    <select id="$race_num" class="custom-select form-control col-sm-10 hr" required>
                                    <script>selectIDS.push($race_num);</script>
HTML;

                            // Horse count
                            $horse_count = isset($_SESSION["site_default_horse_count"]) ?
                                $_SESSION["site_default_horse_count"] : 1;
                            for ($i = 1; $i < $horse_count + 1; $i++) {
                                 $race_HTML .= "<option value='$i'>$i</option>";
                            }
$race_HTML .= <<< HTML
                                        <script>defaultHorseCount = $horse_count </script>
                                    </select>
                                </div>
                                    <div id="addInput$race_num" class="form-row mt-4 addSelect">
HTML;
                            $query = "SELECT horse_number FROM horse WHERE race_event_id = :event_id AND race_race_number = :race_num";
                            $horses = $pdo->prepare($query);
                            $horses->execute(['event_id' => $event_id, 'race_num' => $race_num]);
                            $count_horse = $horses->rowCount();
                            if ($count_horse == $horse_count) {
                                $addHorse = "";
                            }
                            if ($horses->rowCount() > 0) {
                                $row_horse = $horses->fetchAll();
                                $i = 0;
                                while ($i < count($row_horse)) {
                                    $horse_val = $row_horse[$i]["horse_number"];

$race_HTML .= <<< HTML
     
                                                                           
                                            <div class="input-group mb-1 group-horse" id="horse$race_num$i">
                                                <input type="text" name="horses[$race_num][$i]" class="custom-select my-1 mr-sm-2 horse ht" value="$horse_val">
                                              <div class="input-group-append">
                                                <span class="btn btn-danger" id="$race_num$i" style="border-radius: 100px">-</span>
                                                <script>
                                                    deleteHorsesIDS.push($race_num$i);
                                                    racesIDs.push($race_num);
                                                </script>
                                              </div>
                                            </div>
HTML;
                                    $i++;
                                }

                            } else {
                                $count_horse = 1;
$race_HTML .= <<< HTML
                                
                                <input type="text" name="horses[$race_num][]" class="custom-select my-1 mr-sm-2 horse ht" placeholder="Horse#">
HTML;
                            }
$race_HTML .= <<< HTML
                                                </div>
                                              </div>
                                                    <div class="d-flex justify-content-between mt-4">
                                                        <span class="btn btn-success $addHorse" id="addHorse$race_num" style="border-radius: 100px">+</span>
                                                        <a href="/races/?r=$race_num" id="goToRace$race_num" class="btn btn-primary"> Go To Race $race_num </a>
                                                        <a href="#" id="update$race_num" class="btn btn-primary d-none">Update Race $race_num</a>
                                                        <div class="custom-control custom-checkbox">
                                                          <input type="checkbox" class="custom-control-input" id="cancel$race_num" $checked>
                                                          <label class="custom-control-label" for="cancel$race_num">Cancel Race $race_num</label>
                                                        </div>
                                                        <script>
                                                            cancelIds.push("cancel$race_num");
                                                            addHorseIDS.push("addHorse$race_num");
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!---END Race HTML -->
                                        <script>
                                            $( "#$race_num" ).val($count_horse);
                                            if ($count_horse === defaultHorseCount) {
                                                $("#addHorse$race_num").addClass("disabled");
                                            }
                                        </script>
HTML;
                            echo $race_HTML;
                            $index++;
                        }
                    }
                }

                ?>

            </fieldset>
            <div class="text-center mt-4">
                <a href="#" id="addRace" class="btn btn-primary"> Add a Race </a>
                <a href="#" id="deleteRace" class="btn btn-danger disabled">Delete a Race</a>
            </div>
            <div class="text-center mt-3">
                <input type="submit" name="update_event" value="Update Event" class="btn btn-primary d-none">
            </div>
        </form>
    </section>
</main>
<script>
    $('fieldset').on('click', function (e) {
        const idClicked = e.target.id;

        //Delete a Horse
        if (deleteHorsesIDS.includes(parseInt(idClicked))) {
            const id = 'select#' + idClicked.charAt(0);

                const val = $( id ).val();
                if (val > 1) {
                    $( id ).val(val - 1);
                    $('#horse' + idClicked).remove();
                } else {
                    $('#horse' + idClicked + ' input').
                    attr('name', 'horses['+ idClicked.charAt(0) +'][0]');

                    $('#addInput' + idClicked.charAt(0) + ' div.group-horse:first-of-type').
                    attr('id', 'horse' + idClicked.charAt(0) + '0');

                    $('#' + idClicked).addClass('d-none');
                }

                if ( $( '#addHorse' + idClicked.charAt(0) ).hasClass( 'disabled' ) ) {
                    $( '#addHorse' + idClicked.charAt(0) ).removeClass( 'disabled' )
                }

                changeGoToRaceButtonToUpdateRaceButton(idClicked.charAt(0));

        }

        // Cancel a Race
        if (cancelIds.includes(idClicked)) {
            $.ajax({
                url: './race.php?r=' + idClicked.charAt(6)
            }).done(function (data) {
                if (data) {
                    $('#c' + idClicked.charAt(6) ).removeClass('d-none');
                    $('#card' + idClicked.charAt(6) ).addClass('d-none');
                    $('#addHorse' + idClicked.charAt(6) ).addClass('disabled');
                } else if (!data) {
                    $('#card' + idClicked.charAt(6) ).removeClass('d-none');
                    $('#c' + idClicked.charAt(6) ).addClass('d-none');
                    numberOfHorses = $('#addInput' + idClicked.charAt(6) + ' div.group-horse').length;
                    if (numberOfHorses < defaultHorseCount) {
                        $('#addHorse' + idClicked.charAt(6) ).removeClass('disabled');
                    }
                }
            });
        }

        // Add a Horse
        if (addHorseIDS.includes(idClicked)) {
            numberOfHorses = $('#addInput' + idClicked.charAt(8) + ' div.group-horse').length;
            if (numberOfHorses < defaultHorseCount) {
                if ( $('#' + idClicked).hasClass('disabled') ) {
                    $('#' + idClicked).removeClass('disabled');
                }
                if ( $('#addInput' + idClicked.charAt(8) + ' div.group-horse div.input-group-append span').hasClass('d-none') ) {
                    $('#addInput' + idClicked.charAt(8) + ' div.group-horse div.input-group-append span').removeClass('d-none')
                }

                $('#addInput' + idClicked.charAt(8) + ' div.group-horse:last-of-type').clone().
                attr('id', 'horse' + idClicked.charAt(8) + numberOfHorses).
                appendTo('#addInput' + idClicked.charAt(8) );

                $('#horse' + idClicked.charAt(8) + numberOfHorses + ' input:last-of-type').
                attr({
                       name: 'horses['+ idClicked.charAt(8) +']['+ numberOfHorses +']',
                       value: ''
                        });
                numberOfHorses = numberOfHorses + 1;
                $('#' + idClicked.charAt(8) ).val(numberOfHorses);

            }
            if (numberOfHorses === defaultHorseCount) {
                $('#' + idClicked).addClass('disabled');
            }
            changeGoToRaceButtonToUpdateRaceButton(idClicked.charAt(8));
        }

        // Select
        if (selectIDS.includes(parseInt(idClicked))) {
            $('#' + idClicked).on('change', function () {
                numberOfHorses = $('#addInput' + idClicked + ' div.group-horse').length;

                if (this.value < numberOfHorses) {
                    const amountToDecrement = numberOfHorses - this.value;

                    for (let k = 0; k < amountToDecrement; k++) {
                        $('#addInput' + idClicked + ' div.group-horse:last-of-type').remove();

                    }
                    $('#addHorse' + idClicked ).removeClass('disabled');
                }

                if (this.value > numberOfHorses) {
                    for (numberOfHorses; numberOfHorses < this.value; numberOfHorses++) {
                        $('#addInput' + idClicked + ' div.group-horse:last-of-type').clone().
                        attr('id', 'horse' + idClicked + numberOfHorses).
                        appendTo('#addInput' + idClicked);
                        $('#horse' + idClicked + numberOfHorses + ' input').
                        attr({
                            name: 'horses['+ idClicked +']['+ numberOfHorses +']',
                            value: ''
                        });
                    }
                    if (numberOfHorses === defaultHorseCount) {
                        $('#addHorse' + idClicked ).addClass('disabled');
                    }
                }
                changeGoToRaceButtonToUpdateRaceButton(idClicked);

            });
        }

    });
</script>
{footer}
<?php ob_end_flush(); ?>
