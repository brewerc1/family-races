<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Manage an Event";

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
            </select>
        </td>
        <td class='position-relative'>
                <input type='text' id='win1' class='w-100 form-control'>
        </td>
        <td class='position-relative'>
                <input type='text' id='place1' class='w-100 form-control'>
        </td>
        <td class='position-relative'>
                <input type='text' id='show1' class='w-100 form-control'>
        </td>
    </tr>
    <!-- Row C -->
    <tr>
        <td>
            <select  id='place-result' class='custom-select'>
            <option value=''>Horse#</option>
            </select>
        </td>
        <td></td>
        <td class='position-relative'>
                <input type='text' id='place2' class='w-100 form-control'>
        </td>
        <td class='position-relative'>
            <input type='text' id='show2' class='w-100 form-control'>
        </td>
    </tr>
    <!-- Row D -->
    <tr>
        <td>
            <select  id='show-result' class='custom-select'>
            <option value=''>Horse#</option>
            </select>
        </td>
        <td></td>
        <td></td>
        <td class='position-relative'>
            <input type='text' id='show3' class='w-100 form-control'>
        </td>
    </tr>
    </table>
HTML;
$javascript = <<< JAVASCRIPT
    
   
    
    
JAVASCRIPT;
// enterResultFormHTML();

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
    const defaultHorseCount = <?php echo isset($_SESSION["site_default_horse_count"]) ?
        $_SESSION["site_default_horse_count"] : 1; ?>

    $( document ).ready( function () {

        // Done
        // Select (value)
        $('.group').each( function () {
            const id = '#addInput' + this.id.charAt(5) + ' div.group-horse';

            $('#' + this.id.charAt(5)).val($( id ).length);

            //$('#addInput' + (index + 1) + ' div.group-horse input').val()
            let horses = [];
           $(  id + ' input' ).each( function () {
               horses.push( this.value );
           });

            raceHistory.set(parseInt(this.id.charAt(5)), horses);

            // console.log("race history")
            // console.log(raceHistory)

        });

        // Done
        // Select on value change
        $('.group-select').bind('change', function () {
            numberOfHorses = $('#addInput' + this.id + ' div.group-horse').length;

            if (this.value < numberOfHorses) {
                const amountToDecrement = numberOfHorses - this.value;
                removeInput(this.id, amountToDecrement);
                $('#addHorse' + this.id ).removeClass('disabled');
            }

            if (this.value > numberOfHorses) {
                for (numberOfHorses; numberOfHorses < this.value; numberOfHorses++) {
                    $('#addInput' + this.id + ' div.group-horse div.input-group-append span').
                    removeClass('d-none');
                    duplicateHorseInput(this.id, numberOfHorses);
                }
                if (numberOfHorses === defaultHorseCount) {
                    $('#addHorse' + this.id ).addClass('disabled');
                }
            }

        });

        // Done
        // Cancel a race
        $('.cancel-race').bind( 'click', function () {

            const isChecked = $('#' + this.id).is(':checked') ? 1 : 0;

            $.ajax({
                type: 'POST',
                url: './race.php?e=2&r=' + this.id.charAt(6) + '&q=' + 2,
                data: {is_checked: isChecked},
                success: function (data) {
                    $( ".close-btn" ).toggleClass( 'disabled', (isChecked === 1) );
                    $('main').prepend(data);
                    $('#alert').delay( 3000 ).fadeOut( 400 );
                }
            });
        });


    });

    let raceHistory = new Map();
    let racesResultsTrack = new Map();
    let resultList = new Map();
    let numberOfHorses;
    let horsesList;

    // Done
    function removeInput(raceNumber, amountToDecrement) {
        for (let k = 0; k < amountToDecrement; k++) {
            $('#addInput' + raceNumber + ' div.group-horse:last-of-type').remove();

        }
    }

    // Done
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

    // Done
    function updateRace(eventNumber, raceNumber) {
        $('#mainModal div.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

        let horses = [];
        $('#addInput' + raceNumber + ' div.group-horse input').each( function () {
             horses.push(this.value);
        })

        $.ajax({
            type: 'POST',
            url: './race.php?r=' + raceNumber + '&q=' + 3 + '&e=' + eventNumber,
            data: {horse_array: horses},
            success: function (data) {
                $('main').prepend(data);
                $('#alert').delay( 3000 ).fadeOut( 400 );
                raceHistory.set(raceNumber, horses);
            }
        });


    }

    // Done
    function dismiss(raceNumber) {

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

    // Done
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

    // Done
    function openWindow(raceNumber) {
        $.ajax({
            type: 'POST',
            url: './race.php?e=2&r=' + raceNumber + '&q=' + 1,
            data: {open: 0},
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

    // Done
    function closeWindow(raceNumber) {
        $.ajax({
            type: 'POST',
            url: './race.php?e=2&r=' + raceNumber + '&q=' + 1,
            data: {open: 1},
            success: function (data) {
                $('main').prepend(data);
                $('#c' + raceNumber ).removeClass('d-none');
                $('#card' + raceNumber ).addClass('d-none');
                $('#alert').delay( 3000 ).fadeOut( 400 );

            }
        });
    }

    // Done
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

    // Done
    function deleteRace(eventNumber, raceNumber) {
        $('#mainModal div.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

        $.ajax({
            url: './race.php?e=' + eventNumber + '&r=' + raceNumber + '&q=' + 4,
            success: function (data) {
                $('main').prepend(data);
                $('#alert').delay( 3000 ).fadeOut( 400 );

                $('#group' + raceNumber).remove();

                // $('.group').each(function (index) {
                //
                //     if ((index + 1) > raceNumber) {
                //         console.log(index + 1)
                //
                //         $('#btn' + (index + 1)).text('Race ' + index);
                //         $('#c' + (index + 1) + ' div.mt-4 label').text('Cancel Race ' + index);
                //         $('#result' + (index + 1)).text('Enter Result for Race ' + index);
                //         $('#deleteRace' + (index + 1)).text('Delete Race ' + index);
                //         $('#update' + (index + 1)).text('Save Race ' + index);
                //
                //     }
                //
                // });
            }
        });
    }

    // Done
    function depopulateHorses() {
        $( ".race-result" ).each( function () {
            $(".race-result option").remove();
        });

        $('#message table').remove();
        //enterResultFormHTML();
    }

    function enterResultForRace(eventNumber, raceNumber) {
        // $('#mainModal div.modal-footer button:last-of-type').attr('data-dismiss', 'modal');
        $('#collapse' + raceNumber).addClass('show');

        let oldWin = null;
        let oldPlace = null;
        let oldShow = null;

        if (racesResultsTrack.has((raceNumber + 'w')) &&
            racesResultsTrack.has((raceNumber + 'p')) &&
            racesResultsTrack.has((raceNumber + 'w'))) {
            oldWin = racesResultsTrack.get((raceNumber + 'w'));
            oldPlace = racesResultsTrack.get((raceNumber + 'p'));
            oldShow = racesResultsTrack.get((raceNumber + 's'));
        }

        let win = [];
        win.push($('#win-result').val());
        win.push($('#win1').val());
        win.push($('#place1').val());
        win.push($('#show1').val());
        //console.log($('#show1').val())
        // racesResultsTrack.set((raceNumber + 'w'), win);

        let place = [];
        place.push($('#place-result').val());
        place.push($('#place2').val());
        place.push($('#show2').val());
        // racesResultsTrack.set((raceNumber + 'p'), place);

        let show = [];
        show.push($('#show-result').val());
        show.push($('#show3').val());
        // racesResultsTrack.set((raceNumber + 's'), show);

        depopulateHorses();
        let data = {win: win, place: place, show: show}

        if (oldWin != null && oldPlace != null && oldShow != null)
            data = {win: win, place: place, show: show, old_win: oldWin, old_place: oldPlace, old_show: oldShow}

        $.ajax({
            method: 'POST',
            url: './race.php?e=' + eventNumber + '&r=' + raceNumber + '&q=' + 5,
            data: data,
            dataType: 'json',
            success: function (data) {
                $('main').prepend(data['alert']);
                $('#alert').delay( 3000 ).fadeOut( 400 );
                if (data['saved'] === 1) {
                    resultWereEnteredForRace(eventNumber, raceNumber);
                    racesResultsTrack.set((raceNumber + 'w'), win);
                    racesResultsTrack.set((raceNumber + 'p'), place);
                    racesResultsTrack.set((raceNumber + 's'), show);
                }
            }
        });
    }


    // TOdo fix the inputs and select options
    function enterResultFormHTML() {
        $('.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

        $('#message').html("<table class='table table-borderless'>\n" +
            "    <!-- Row A -->\n" +
            "    <thead>\n" +
            "        <tr>\n" +
            "          <th scope='col'>Horse#</th>\n" +
            "          <th scope='col'>_Win__</th>\n" +
            "          <th scope='col'>Place</th>\n" +
            "          <th scope='col'>Show</th>\n" +
            "        </tr>\n" +
            "    </thead>\n" +
            "    <!-- Row B -->\n" +
            "    <tr>\n" +
            "        <td>\n" +
            "            <select  id='win-result' class=' race-result w-100' required>\n" +
            "            </select>\n" +
            "        </td>\n" +
            "        <td class='position-relative'>\n" +
            "                <input type='text' id='win1' class='w-100' required>\n" +
            "        </td>\n" +
            "        <td class='position-relative'>\n" +
            "                <input type='text' id='place1' class='w-100' required>\n" +
            "        </td>\n" +
            "        <td class='position-relative'>\n" +
            "                <input type='text' id='show1' class='w-100' required>\n" +
            "        </td>\n" +
            "    </tr>\n" +
            "    <!-- Row C -->\n" +
            "    <tr>\n" +
            "        <td>\n" +
            "            <select  id='place-result' class=' race-result' required>\n" +
            "            </select>\n" +
            "        </td>\n" +
            "        <td></td>\n" +
            "        <td class='position-relative'>\n" +
            "                <input type='text' id='place2' class='w-100' required>\n" +
            "        </td>\n" +
            "        <td class='position-relative'>\n" +
            "            <input type='text' id='show2' class='w-100' required>\n" +
            "        </td>\n" +
            "    </tr>\n" +
            "    <!-- Row D -->\n" +
            "    <tr>\n" +
            "        <td>\n" +
            "            <select  id='show-result' class=' race-result' required>\n" +
            "            </select>\n" +
            "        </td>\n" +
            "        <td></td>\n" +
            "        <td></td>\n" +
            "        <td class='position-relative'>\n" +
            "            <input type='text' id='show3' class='w-100' required>\n" +
            "        </td>\n" +
            "    </tr>\n" +
            "    </table>");
    }

    // Done
    function populateHorses(raceNumber) {
        enterResultFormHTML();
        horsesList = raceHistory.get(raceNumber);

        $( ".race-result" ).prepend( "<option>Horse#</option>" );
        horsesList.forEach(horse => {
            $('#win-result option:last-of-type').clone().
            attr('value', horse).text(horse).appendTo('.race-result');
        })

        if (racesResultsTrack.has((raceNumber + 'w'))) {
            $('#win-result').val(racesResultsTrack.get((raceNumber + 'w'))[0]);
            $('#win1').val(racesResultsTrack.get((raceNumber + 'w'))[1]);
            $('#place1').val(racesResultsTrack.get((raceNumber + 'w'))[2]);
            $('#show1').val(racesResultsTrack.get((raceNumber + 'w'))[3]);
        }

        if (racesResultsTrack.has((raceNumber + 'p'))) {
            $('#place-result').val(racesResultsTrack.get((raceNumber + 'p'))[0]);
            $('#place2').val(racesResultsTrack.get((raceNumber + 'p'))[1]);
            $('#show2').val(racesResultsTrack.get((raceNumber + 'p'))[2]);
        }

        if (racesResultsTrack.has((raceNumber + 's'))) {
            $('#show-result').val(racesResultsTrack.get((raceNumber + 's'))[0]);
            $('#show3').val(racesResultsTrack.get((raceNumber + 's'))[1]);
        }

    }

    // Done
    // can't run in document.ready
    function resultWereEnteredForRace(eventNumber, raceNumber) {
        $('#c' + raceNumber + ' div.custom-control').remove();
        $('#open' + raceNumber).remove();
        $('#result' + raceNumber).text('Edit Result for race ' + raceNumber).
        attr('class', 'btn btn-secondary');
        $('#c' + raceNumber + ' span').text('Results were entered successfully.');

        if (!racesResultsTrack.has((raceNumber + 'w')) &&
            !racesResultsTrack.has((raceNumber + 'p')) &&
            !racesResultsTrack.has((raceNumber + 's'))) {

            $.ajax({
                method: 'POST',
                url: './race.php?e=' + eventNumber + '&r=' + raceNumber + '&q=' + 6,
                dataType: 'json',
                success: function (data) {
                    let win = [data['win']['horse_number'],
                        data['win']['win_purse'], data['win']['place_purse'],
                        data['win']['show_purse']];
                    let place = [data['place']['horse_number'],
                        data['place']['place_purse'], data['place']['show_purse']]
                    let show = [data['show']['horse_number'], data['show']['show_purse']];
                    racesResultsTrack.set((raceNumber + 'w'), win);
                    racesResultsTrack.set((raceNumber + 'p'), place);
                    racesResultsTrack.set((raceNumber + 's'), show);
                }
            });
        }
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
<!--                    <div class="input-group-append">-->
<!--                        <span class="input-group-text">.00</span>-->
<!--                    </div>-->
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
                            $race_num_if_result_were_entered = "";
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
                <div class="group border-bottom border-dark" id="group$race_num">
                   <div class="d-flex flex-row">
                       <button id="btn$race_num" class="btn btn-block dropdown-toggle dt" type="button" 
                       data-toggle="collapse" data-target="#collapse$race_num" aria-expanded="true" 
                       aria-controls="collapseOne">
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
                                <span></span>
                                <div class="custom-control custom-checkbox mt-4">
                                    <input type="checkbox" class="custom-control-input cancel-race" id="cancel$race_num" $checked>
                                    <label class="custom-control-label" for="cancel$race_num">Cancel Race $race_num</label>
                                </div>
                                <div class="text-center card-body px-3">
                                    <a href="#" class="btn btn-primary $disabled close-btn" id="result$race_num"
                                            data-toggle="modal" 
                                            data-target="#mainModal" 
                                            data-title="Race $race_num Results"
                                            data-button-primary-text="Save" 
                                            data-button-primary-action="enterResultForRace($event_id, $race_num)" 
                                            data-button-secondary-text="Exit" 
                                            data-button-secondary-action="depopulateHorses()"
                                             onclick="populateHorses($race_num)"
                                    >Enter Results for Race $race_num</a>
                                    <a href="#" class="btn btn-secondary $disabled close-btn" id="open$race_num" 
                                     onclick="openWindow($race_num)">Reopen Betting Window</a>
                                </div>
                        </div>
                        <div class="card-body $display_none" id="card$race_num">
                                <div class="d-flex flex-row-reverse mb-2">
                                    <a href="#" id="deleteRace$race_num" class="btn btn-outline-danger del-group"
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
                                    <select id="$race_num" class="custom-select form-control col-sm-10 group-select" required>
                                    
HTML;

                            // Horse count
                            $horse_count = isset($_SESSION["site_default_horse_count"]) ?
                                $_SESSION["site_default_horse_count"] : 1;

                            for ($i = 1; $i < $horse_count + 1; $i++) {
                                 $race_HTML .= "<option value='$i'>$i</option>";
                            }
$race_HTML .= <<< HTML
                                    </select>
                                </div>
                                    <div id="addInput$race_num" class="form-row mt-4 addSelect">
HTML;
                            $query = "SELECT horse_number, finish FROM horse WHERE race_event_id = :event_id AND race_race_number = :race_num";
                            $horses = $pdo->prepare($query);
                            $horses->execute(['event_id' => $event_id, 'race_num' => $race_num]);
                            $span_d_none = ($horses->rowCount() == 1) ? "d-none" : "";
                            if ($horses->rowCount() == $horse_count) {
                                $addHorse = "";
                            }
                            if ($horses->rowCount() > 0) {
                                $row_horse = $horses->fetchAll();
                                $i = 0;
                                while ($i < count($row_horse)) {
                                    //var_dump($row_horse[$i]);
                                    $horse_val = $row_horse[$i]["horse_number"];
                                    $finish[$race_num - 1][$i] = $row_horse[$i]["finish"];


                                    // ids
                                    $parent_div = "horse" . $race_num.$i . substr(microtime() . "", 2, 5);
                                    $input_id = "id" . $race_num.$i . substr(microtime() . "", 2, 5);
                                    $delete_id = $race_num.$i . substr(microtime() . "", 2, 5);

$race_HTML .= <<< HTML
     
                                            <div class="input-group mb-1 group-horse" id="$parent_div">
                                                <input type="text" id="$input_id" name="horses[$race_num][$i]" 
                                                class="custom-select my-1 mr-sm-2" 
                                                value="$horse_val">
                                              <div class="input-group-append">
                                                <span class="btn btn-danger $span_d_none" id="$delete_id" onclick="deleteHorse('$parent_div', '$delete_id')" 
                                                style="border-radius: 100px">-</span>
                                              </div>
                                            </div>
HTML;
                                    $i++;
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
                                    class="custom-select my-1 mr-sm-2">
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
                                        </div> <!---END Race HTML -->
HTML;

                                // Check if result exist in the DB
                            if (key_exists(($race_num - 1), $finish)) {
                                for ($k = 0; $k < count($finish[$race_num - 1]); $k++) {
                                    //echo count($finish[$race_num - 1]);

                                    if (!empty($finish[$race_num - 1][$k])) {
                                        $race_HTML .= <<< HTML
                                                <script>
                                                        $( document ).ready(function() {
                                                            resultWereEnteredForRace($event_id, $race_num);
                                                        })
                                                </script>
HTML;
                                        break;
                                    }
                                }
                            }

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
{footer}
<?php ob_end_flush(); ?>
