<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Manage an Event";


$javascript = <<< JAVASCRIPT

    
JAVASCRIPT;


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

$node_options = "";
$HTML_for_Race_result = <<< HTML

    <table class='table table-borderless'>
    <!-- Row A -->
    <thead>
        <tr>
          <th scope='col'>Horse#</th>
          <th scope='col'>Win</th>
          <th scope='col'>Place</th>
          <th scope='col'>Show</th>
        </tr>
    </thead>
    <!-- Row B -->
    <tr>
        <td>
            <select  id='win-result' class='custom-select'>
            <option value=''>Horse#</option>
            $node_options
            </select>
        </td>
        <td class='position-relative'><input type='text' class='w-100' id='win1'></td>
        <td class='position-relative'><input type='text' class='w-100' id='place1'></td>
        <td class='position-relative'><input type='text' class='w-100' id='show1'></td>
    </tr>
    <!-- Row C -->
    <tr>
        <td>
            <select  id='place-result' class='custom-select'>
            <option value=''>Horse#</option>
            $node_options
            </select>
        </td>
        <td></td>
        <td class='position-relative'><input type='text' class='w-100' id='place2'></td>
        <td class='position-relative'><input type='text' class='w-100' id='show2'></td>
    </tr>
    <!-- Row D -->
    <tr>
        <td>
            <select  id='show-result' class='custom-select'>
            <option value=''>Horse#</option>
            $node_options
            </select>
        </td>
        <td></td>
        <td></td>
        <td class='position-relative'><input type='text' class='w-100' id='show3'></td>
    </tr>
    </table>
HTML;

?>
{header}
{main_nav}
<script>
    let raceHistory = new Map();

    let selectIDS = new Set();
    let showCancelIDS = new Set();
    let defaultHorseCount;
    let numberOfHorses;
    let horsesList;

    // function changeGoToRaceButtonToUpdateRaceButton(raceNumber) {
    //     $('#update' + raceNumber).removeClass('d-none');
    //     $('#goToRace' + raceNumber).addClass('d-none');
    //     $('#wind' + raceNumber).addClass('disabled');
    //     $('#open' + raceNumber).addClass('disabled');
    //
    // }

    function removeInput(raceNumber, amountToDecrement) {
        for (let k = 0; k < amountToDecrement; k++) {
            $('#addInput' + raceNumber + ' div.group-horse:last-of-type').remove();

        }
    }

    function duplicateHorseInput(raceNumber, numberOfCurrentHorsesInput) {
        const parentDivId = 'horse' + raceNumber + numberOfCurrentHorsesInput + Date.now();
        const inputId = 'id' + raceNumber + numberOfCurrentHorsesInput + Date.now();
        const spanId = raceNumber + numberOfCurrentHorsesInput + Date.now();

        $('#addInput' + raceNumber + ' div.group-horse:last-of-type').clone().
        attr('id', parentDivId).appendTo('#addInput' + raceNumber );

        $('#' + parentDivId + ' input').
        attr({
            name: 'horses['+ raceNumber +']['+ numberOfCurrentHorsesInput +']',
            value: '',
            id: inputId,
            onchange: "inputOnChange('" + inputId + "')"
        });

        $('#' + parentDivId + ' div.input-group-append span').
        attr({
            id: spanId,
            onclick: "deleteHorse('" + parentDivId + "', '" + spanId + "')"
        });
    }

    function updateRace(eventNumber, raceNumber) {
        $('#mainModal div.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

            let horses = [];
            $('#addInput' + raceNumber + ' div.group-horse input').each( function () {
                horses.push(this.value);
            })
            $.post('./race.php?r=' + raceNumber + '&q=' + 3 + '&e=' + eventNumber , {
                horse_array: horses
            }).done( function (data) {
                $('main').prepend(data);
                $('#alert').delay( 3000 ).fadeOut( 400 );
            });

        raceHistory.set(raceNumber, horses);

        // $('#update' + raceNumber).addClass('d-none');
        // $('#goToRace' + raceNumber).removeClass('d-none');
        // $('#wind' + raceNumber).removeClass('disabled');
    }

    function dismiss(raceNumber) {
        // $('#update' + raceNumber).addClass('d-none');
        // $('#goToRace' + raceNumber).removeClass('d-none');
        // $('#wind' + raceNumber).removeClass('disabled');

        const inputId = '#addInput' + raceNumber + ' div.group-horse';
        numberOfHorses = $(inputId).length;
        const length = raceHistory.get(raceNumber).length;
        if (numberOfHorses <= length) {
            let i = length - numberOfHorses;
            for (let j = 0; j < i; j++) {
                duplicateHorseInput(raceNumber, (numberOfHorses + j));
            }
        } else {
            let i = numberOfHorses - length;
            removeInput(raceNumber, i);
        }

        $(inputId).each(function (index) {
            $(inputId + ':nth-child(' + (index + 1) + ') input').val(raceHistory.get(raceNumber)[index]);
            $(inputId + ':nth-child(' + (index + 1) + ') div.input-group-append span').removeClass('d-none');
        });

        $('#' + raceNumber).val(length);
        numberOfHorses = $(inputId).length;
        if (numberOfHorses < defaultHorseCount)
            $('#addHorse' + raceNumber).removeClass('disabled');
    }

    function deleteHorse(parentDivId, selfId) {
        const id = 'select#' + parentDivId.charAt(5);

        const val = $( id ).val();
        if (val > 1) {
            $( id ).val(val - 1);
            $('#' + parentDivId).remove()
        } else {
            $('#' + parentDivId + ' input').
            attr('name', 'horses['+ parentDivId.charAt(5) +'][0]');

            $('#' + selfId).addClass('d-none');
        }

        if ( $( '#addHorse' + parentDivId.charAt(5) ).hasClass( 'disabled' ) ) {
            $( '#addHorse' + parentDivId.charAt(5) ).removeClass( 'disabled' )
        }
    }

    function openWindow(raceNumber) {
        $.ajax({
            url: './race.php?r=' + raceNumber + '&q=' + 1,
            success: function (data) {
                $('main').prepend(data);
                $('#c' + raceNumber ).addClass('d-none');
                $('#card' + raceNumber ).removeClass('d-none');
                $('#alert').delay( 3000 ).fadeOut( 400 );

                numberOfHorses = $('#addInput' + raceNumber + ' div.group-horse').length;
                if (numberOfHorses < defaultHorseCount)
                    $('#addHorse' + raceNumber).removeClass('disabled');
            }
        });
    }

    function closeWindow(raceNumber) {
        $.ajax({
            url: './race.php?r=' + raceNumber + '&q=' + 1,
            success: function (data) {
                $('main').prepend(data);
                $('#c' + raceNumber ).removeClass('d-none');
                $('#card' + raceNumber ).addClass('d-none');
                $('#alert').delay( 3000 ).fadeOut( 400 );
            }
        });
    }

    function cancelRace(raceNumber) {
        $.ajax({
            url: './race.php?r=' + raceNumber + '&q=' + 2,
            success: function (data) {
                $('main').prepend(data);
                $('#alert').delay( 3000 ).fadeOut( 400 );
                if ( $('#cancel' + raceNumber).is(':checked') ) {
                    $('#open' + raceNumber).addClass('disabled');
                    $('#result' + raceNumber).addClass('disabled');
                } else {
                    $('#open' + raceNumber).removeClass('disabled');
                    $('#result' + raceNumber).removeClass('disabled');
                }
            }
        });
    }

    function addHorse(btnId) {
        numberOfHorses = $('#addInput' + btnId.charAt(8) + ' div.group-horse').length;
        if (numberOfHorses < defaultHorseCount && !$('#' + btnId).hasClass('disabled') ) {
            if ( $('#addInput' + btnId.charAt(8) + ' div.group-horse div.input-group-append span').hasClass('d-none') ) {
                $('#addInput' + btnId.charAt(8) + ' div.group-horse div.input-group-append span').removeClass('d-none')
            }
            duplicateHorseInput(btnId.charAt(8), numberOfHorses);
            numberOfHorses = numberOfHorses + 1;
            $('#' + btnId.charAt(8) ).val(numberOfHorses);
        }
        if (numberOfHorses === defaultHorseCount) {
            $('#' + btnId).addClass('disabled');
        }
    }

    // function inputOnChange(inputId) {
    //     changeGoToRaceButtonToUpdateRaceButton(inputId.charAt(2));
    // }

    // Under Construction
    function deleteRace(eventNumber, raceNumber) {
        $('#mainModal div.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

        $.ajax({
            url: './race.php?e=' + eventNumber + '&r=' + raceNumber + '&q=' + 4,
            success: function (data) {
                $('main').prepend(data);
                $('#alert').delay( 3000 ).fadeOut( 400 );

                $('.group').each(function (index) {
                    if ((index + 1) >= raceNumber) {
                        console.log(index);
                    }
                });
            }
        });
    }

    function resultWereEnteredForRace(raceNumber) {

    }

    function enterResultForRace(raceNumber) {
        $('#mainModal div.modal-footer button:last-of-type').attr('data-dismiss', 'modal');
        $('#collapse' + raceNumber).addClass('show');

        // console.log($('#win-result').val() + " Win: $ " +$('#win1').val());
        // console.log($('#win-result').val() + " Place: $ " + $('#place1').val());
        // console.log($('#win-result').val() + " Show: $ " + $('#show1').val());
        // console.log($('#place-result').val() + " Place: $ " + $('#place2').val());
        // console.log($('#place-result').val() + " Show: $ " + $('#show2').val());
        // console.log($('#show-result').val() + " Show: $ " + $('#show3').val());
    }

    function populateHorses(raceNumber) {
        horsesList = raceHistory.get(raceNumber);
        console.log(horsesList);

        $('#mainModal td div#show-result').append("<h1>hi</h1>");
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


                $query = "SELECT race_number, window_closed, window_closed, cancelled FROM race WHERE event_id = :event_id";
                $races = $pdo->prepare($query);
                if ($races->execute(['event_id' => $event_id])) {
                    if ($races->rowCount() > 0) {
                        $row = $races->fetchAll();
                        $index = 0;
                        while ($index < count($row)) {
                            $race_num = $row[$index]["race_number"];

                            $checked = $row[$index]["cancelled"] ? "checked" : "";
                            $disabled = $row[$index]["cancelled"] ? "disabled" : "";

                            $display_none = "";
                            $addHorse = "";
                            $closed = "d-none";
                            if ($row[$index]["window_closed"]) {
                                $display_none = "d-none";
                                $closed = "";
                                $addHorse = "disabled";
                            }

$race_HTML = <<< HTML
                <!--- Race HTML -->
                <div class="group border-bottom border-dark">
                   <div class="d-flex flex-row">
                       <button id="btn$race_num" class="btn btn-block dropdown-toggle dt" type="button" data-toggle="collapse" data-target="#collapse$race_num" aria-expanded="true" aria-controls="collapseOne">
                            Race $race_num
                        </button>
                        <a href="/races/?r=$race_num" class="btn btn-outline-primary mb-1"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-in-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M8.146 11.354a.5.5 0 0 1 0-.708L10.793 8 8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0z"/>
                          <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 1 8z"/>
                          <path fill-rule="evenodd" d="M13.5 14.5A1.5 1.5 0 0 0 15 13V3a1.5 1.5 0 0 0-1.5-1.5h-8A1.5 1.5 0 0 0 4 3v1.5a.5.5 0 0 0 1 0V3a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v10a.5.5 0 0 1-.5.5h-8A.5.5 0 0 1 5 13v-1.5a.5.5 0 0 0-1 0V13a1.5 1.5 0 0 0 1.5 1.5h8z"/>
                        </svg></a>
                    </div>
                    <div id="collapse$race_num" class="collapse race" data-parent="#accordion01">
                        <div class="text-center card-body $closed" id="c$race_num">
                                <h4>The betting window has closed.</h4>
                                <div class="custom-control custom-checkbox mt-4">
                                    <input type="checkbox" class="custom-control-input" id="cancel$race_num" $checked 
                                    onclick="cancelRace($race_num)">
                                    <label class="custom-control-label" for="cancel$race_num">Cancel Race $race_num</label>
                                </div>
                                <div class="text-center card-body px-3">
                                    <a href="#" class="btn btn-primary $disabled" id="result$race_num"
                                            data-toggle="modal" 
                                            data-target="#mainModal" 
                                            data-title="Race $race_num Results" 
                                            data-message= "$HTML_for_Race_result"
                                            data-button-primary-text="Save" 
                                            data-button-primary-action="enterResultForRace($race_num)" 
                                            data-button-secondary-text="Exit" 
                                            data-button-secondary-action=""
                                            onclick="populateHorses($race_num)"
                                    >Enter Results for Race $race_num</a>
                                    <a href="#" class="btn btn-secondary $disabled" id="open$race_num" 
                                     onclick="openWindow($race_num)">Reopen Betting Window</a>
                                </div>
                        </div>
                        <div class="card-body $display_none" id="card$race_num">
                                <div class="d-flex flex-row-reverse mb-2">
                                    <a href="#" id="deleteRace$race_num" class="btn btn-outline-danger"
                                        data-toggle="modal" 
                                        data-target="#mainModal" 
                                        data-title="Delete Race $race_num" 
                                        data-message="Are you sure you want to delete Race $race_num?"
                                        data-button-primary-text="Confirm" 
                                        data-button-primary-action="deleteRace('$event_id', '$race_num')" 
                                        data-button-secondary-text="Cancel" 
                                        data-button-secondary-action=""
                                    >Delete Race $race_num</a>
                                </div>
                                <div class="form-row">
                                    <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                    <select id="$race_num" class="custom-select form-control col-sm-10" required>
                                    <script>selectIDS.add($race_num); </script>
HTML;

                            // Horse count
                            $horse_count = isset($_SESSION["site_default_horse_count"]) ?
                                $_SESSION["site_default_horse_count"] : 1;

                            for ($i = 1; $i < $horse_count + 1; $i++) {
                                 $race_HTML .= "<option value='$i'>$i</option>";
                            }
$race_HTML .= <<< HTML
                                        <script>defaultHorseCount = $horse_count; let horsesHistory$race_num = []; </script>
                                    </select>
                                </div>
                                    <div id="addInput$race_num" class="form-row mt-4 addSelect">
HTML;
                            $query = "SELECT horse_number, finish FROM horse WHERE race_event_id = :event_id AND race_race_number = :race_num";
                            $horses = $pdo->prepare($query);
                            $horses->execute(['event_id' => $event_id, 'race_num' => $race_num]);
                            $count_horse = $horses->rowCount();
                            $span_d_none = ($horses->rowCount() == 1) ? "d-none" : "";
                            if ($count_horse == $horse_count) {
                                $addHorse = "";
                            }
                            if ($horses->rowCount() > 0) {
                                $row_horse = $horses->fetchAll();
                                $i = 0;
                                while ($i < count($row_horse)) {
                                    $horse_val = $row_horse[$i]["horse_number"];
                                    $finish[$i] = $row_horse[$i]["finish"];

                                    // ids
                                    $parent_div = "horse" . $race_num.$i . substr(microtime() . "", 2, 5);
                                    $input_id = "id" . $race_num.$i . substr(microtime() . "", 2, 5);
                                    $delete_id = $race_num.$i . substr(microtime() . "", 2, 5);

$race_HTML .= <<< HTML
     
                                            <div class="input-group mb-1 group-horse" id="$parent_div">
                                                <input type="text" id="$input_id" name="horses[$race_num][$i]" 
                                                class="custom-select my-1 mr-sm-2" 
                                                value="$horse_val" onchange="inputOnChange('$input_id')">
                                              <div class="input-group-append">
                                                <span class="btn btn-danger $span_d_none" id="$delete_id" onclick="deleteHorse('$parent_div', '$delete_id')" 
                                                style="border-radius: 100px">-</span>
                                                <script>
                                                    horsesHistory$race_num.push("$horse_val");
                                                </script>
                                              </div>
                                            </div>
HTML;
                                    $i++;
                                }
                                for ($i = 0; $i < count($finish); $i++) {
                                    if (!empty($finish[$i])) {
$race_HTML .= <<< HTML
                                            <script>resultWereEnteredForRace($race_num);</script>
HTML;
                                        break;
                                    }
                                }

                            } else {
                                $count_horse = 1;
                                // ids
                                $parent_div = $race_num.$i . substr(microtime() . "", 2, 5);
                                $input_id = "id" . $race_num.$i . substr(microtime() . "", 2, 5);
                                $delete_id = $race_num.$i . substr(microtime() . "", 2, 5);
$race_HTML .= <<< HTML
                                
                               <div class="input-group mb-1 group-horse" id="horse$parent_div">
                                    <input type="text" id="$input_id" name="horses[$race_num][]" 
                                    class="custom-select my-1 mr-sm-2" onchange="inputOnChange('$input_id')">
                                    <div class="input-group-append">
                                        <span class="btn btn-danger" id="$delete_id" style="border-radius: 100px"
                                        onclick="deleteHorse('horse$parent_div', '$delete_id')">-</span>
                                    </div>
                               </div>
HTML;
                            }
$race_HTML .= <<< HTML
                                                </div>
                                                    <div class="d-flex justify-content-between mt-4">
                                                        <span class="btn btn-success $addHorse" id="addHorse$race_num" style="border-radius: 100px"
                                                        onclick="addHorse('addHorse$race_num')">+</span>
                                                        <a href="#" class="btn btn-primary" id="update$race_num"
                                                            data-toggle="modal" 
                                                            data-target="#mainModal" 
                                                            data-title="Save Changes for Race $race_num" 
                                                            data-message="Are you sure you want to update Race $race_num?"
                                                            data-button-primary-text="Confirm" 
                                                            data-button-primary-action="updateRace($event_id, $race_num)" 
                                                            data-button-secondary-text="Cancel" 
                                                            data-button-secondary-action="dismiss($race_num)"
                                                        >Save Race $race_num</a>
                                                        <a href="#" class="btn btn-primary" id="wind$race_num" 
                                                        onclick="closeWindow($race_num)">Close betting window</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                $( "#$race_num" ).val($count_horse);
                                                if ($count_horse === defaultHorseCount) {
                                                    $("#addHorse$race_num").addClass("disabled");
                                                }
                                                raceHistory.set($race_num, horsesHistory$race_num);
                                            </script>
                                        </div> <!---END Race HTML -->
                                        
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
                <!--<a href="#" id="deleteRace" class="btn btn-danger disabled">Delete a Race</a>-->
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

        // Select
        if (selectIDS.has(parseInt(idClicked))) {
            $('#' + idClicked).on('change', function () {
                numberOfHorses = $('#addInput' + idClicked + ' div.group-horse').length;

                if (this.value < numberOfHorses) {
                    const amountToDecrement = numberOfHorses - this.value;
                    removeInput(idClicked, amountToDecrement);
                    $('#addHorse' + idClicked ).removeClass('disabled');
                }

                if (this.value > numberOfHorses) {
                    for (numberOfHorses; numberOfHorses < this.value; numberOfHorses++) {
                        $('#addInput' + idClicked + ' div.group-horse div.input-group-append span').
                        removeClass('d-none');
                        duplicateHorseInput(idClicked, numberOfHorses);
                    }
                    if (numberOfHorses === defaultHorseCount) {
                        $('#addHorse' + idClicked ).addClass('disabled');
                    }
                }
            });
        }


    });


    // showCancelIDS.forEach(id => {
    //     $('#' + id + ' h4').text("The betting window has closed. Results were successfully entered.");
    //     $('#' + id + ' div label' ).attr('for', '');
    //     $('#' + id + ' a').remove();
    //     $('#btn' + id.charAt(1)).addClass('text-success');
    // });
</script>
{footer}
<?php ob_end_flush(); ?>
