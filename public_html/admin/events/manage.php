<?php

// Refactoring in Progress

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Manage an Event";

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
$disabled_add_race_button = "disabled";
$jackpot_btn_none = "d-none";


$e = isset($_GET["e"]) ? $_GET["e"] : 0;
$event_id = filter_var($e, FILTER_VALIDATE_INT);

// Check if results have been entered
$finish = array();

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
        $event_pot = $row["pot"];
        $disabled_add_race_button = "";
        $jackpot_btn_none = "";
    }
}

$debug = debug();

?>
{header}
{main_nav}
<script>
    const defaultHorseCount = <?php echo isset($_SESSION["site_default_horse_count"]) ?
        $_SESSION["site_default_horse_count"] : 1; ?>;
    const eventNumber = <?php echo $event_id; ?>;

    let raceHorses = new Map();
    let racesResultsTrack = new Map();
    let resultList = new Map();
    let numberOfHorses;
    let horsesList;

    function updateNumberOfHorsesInputValue() {
        $('.group').each( function () {
            const id = '#addInput' + this.id.charAt(5) + ' div.group-horse';
            $('#' + this.id.charAt(5)).val($( id ).length);
            let horses = [];
            $(  id + ' input' ).each( function () {
                horses.push( this.value );
            });

            raceHorses.set(parseInt(this.id.charAt(5)), horses);
        });
    }


    function bindOnChangeOnSelectMenu() {
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
    }

    function bindOnClickCancelRace() {
        $('.cancel-race').bind( 'click', function () {

            const isChecked = $('#' + this.id).is(':checked') ? 1 : 0;

            $.ajax({
                type: 'POST',
                url: './race.php?e='+ eventNumber +'&r=' + this.id.charAt(6) + '&q=' + 2,
                data: {is_checked: isChecked},
                success: function (data) {
                    $( ".close-btn" ).toggleClass( 'disabled', (isChecked === 1) );
                    $('main').prepend(data);
                    $('#alert').delay( 3000 ).fadeOut( 400 );
                }
            });
        });
    }

    function removeInput(raceNumber, amountToDecrement) {
        for (let k = 0; k < amountToDecrement; k++) {
            $('#addInput' + raceNumber + ' div.group-horse:last-of-type').remove();

        }
    }

    function duplicateHorseInput(raceNumber, numberOfCurrentHorsesInput) {
        const parentDivId = 'horse' + raceNumber + numberOfCurrentHorsesInput + Date.now();
        const inputId = 'id' + raceNumber + numberOfCurrentHorsesInput + Date.now();
        const spanId = raceNumber + numberOfCurrentHorsesInput + Date.now();

        $('#addInput' + raceNumber + ' div.group-horse:first-of-type').clone().
        attr('id', parentDivId).appendTo('#addInput' + raceNumber );

        $('#' + parentDivId + ' input').
        attr({
            name: 'horses['+ raceNumber +']['+ numberOfCurrentHorsesInput +']',
            value: '',
            id: inputId
        });

        $('#' + parentDivId + ' div.input-group-append span').
        attr({
            id: spanId,
            onclick: "deleteHorse('" + parentDivId + "', '" + spanId + "')"
        });
    }

    function updateRace(raceNumber) {
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
                raceHorses.set(raceNumber, horses);
            }
        });

    }

    function dismiss(raceNumber) {

        const inputId = '#addInput' + raceNumber + ' div.group-horse';
        numberOfHorses = $(inputId).length;
        const length = raceHorses.get(raceNumber).length;
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
            $(inputId + ':nth-child(' + (index + 1) + ') input').val(raceHorses.get(raceNumber)[index]);
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

    function deleteRace(raceNumber) {
        $('#mainModal div.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

        $.ajax({
            url: './race.php?e=' + eventNumber + '&r=' + raceNumber + '&q=' + 4,
            dataType: 'json',
            success: function (data) {
                $('main').prepend(data['alert']);
                $('#alert').delay( 3000 ).fadeOut( 400 );

                if (data['deleted'] === 1) {
                    $('#group' + raceNumber).remove();
                    raceHorses.delete(raceNumber);
                    console.log(raceHorses)
                }

            }
        });
    }

    function populateHorses(raceNumber) {
        enterResultFormHTML();

        horsesList = raceHorses.get(raceNumber);

        $( ".race-result" ).prepend( "<option value='0'>Horse#</option>" );
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

    function depopulateHorses() {
        $( ".race-result" ).each( function () {
            $(".race-result option").remove();
        });

        $('#message table').remove();
    }

    function enterResultForRace(eventNumber, raceNumber) {
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

        let place = [];
        place.push($('#place-result').val());
        place.push($('#place2').val());
        place.push($('#show2').val());

        let show = [];
        show.push($('#show-result').val());
        show.push($('#show3').val());

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

    function resultWereEnteredForRace(raceNumber) {
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

    function bettingWindow(raceNumber, isOpen, firstId, secondId, del=0) {
        $.ajax({
            type: 'POST',
            url: './race.php?e='+ eventNumber +'&r=' + raceNumber + '&q=' + 1,
            data: {open: isOpen},
            success: function (data) {
                $('main').prepend(data);
                $( firstId ).addClass('d-none');
                $( secondId ).removeClass('d-none');
                $('#alert').delay( 3000 ).fadeOut( 400 );

                if (del === 1) {
                    numberOfHorses = $('#addInput' + raceNumber + ' div.group-horse').length;
                    if (numberOfHorses < defaultHorseCount)
                        $('#addHorse' + raceNumber).removeClass('disabled');
                    else $('#addHorse' + raceNumber).addClass('disabled');
                }

            }
        });
    }

    function openWindow(raceNumber) {
        bettingWindow(raceNumber, 0, ('#c' + raceNumber), ('#card' + raceNumber), 1);
    }
    function closeWindow(raceNumber) {
        bettingWindow(raceNumber, 1, ('#card' + raceNumber), ('#c' + raceNumber));
    }

    function addRace() {
        // Get the last race Number
        // New race number is the last race number plus one
        let keys = Array.from(raceHorses.keys());
        const raceNumber = keys[keys.length - 1] + 1;
        $.ajax({
            type: 'POST',
            url: './race.php?e=' + eventNumber + '&r=' + raceNumber + '&q=' + 7,
            data: {race: raceNumber},
            dataType: 'json',
            success: function (data) {
                $('main').prepend(data['alert']);
                $('#alert').delay( 3000 ).fadeOut( 400 );
                if (data['created'] === 1) {
                    raceHorses.set(raceNumber, ['']);
                    console.log(raceHorses)

                    // UI
                    const groupId = 'group' + raceNumber;
                    $('#group0').clone().removeClass('d-none').attr('id', groupId ).appendTo('#accordion01');
                    $('#' + groupId + ' button#btn0').text('Race ' + raceNumber).
                    attr('id', 'btn' + raceNumber).
                    attr('data-target', '#collapse' + raceNumber);
                    $('#' + groupId + ' div.d-flex:first-of-type a:first-of-type').
                    attr('href', '/races/?e=' + eventNumber + '&r' + raceNumber);

                    const collapseId = 'collapse' + raceNumber;
                    $('#' + groupId + ' div#collapse0').attr('id', collapseId);

                    const cId = 'c' + raceNumber;
                    $('#' + collapseId + ' div#c0').attr('id', cId);
                    $('#' + cId + ' div.custom-checkbox input').attr('id', 'cancel' + raceNumber);
                    $('#' + cId + ' div.custom-checkbox label').text('Cancel Race ' + raceNumber)
                        .attr('for', 'cancel' + raceNumber);
                    $('#' + cId + ' div.card-body a#result0').text('Enter Results for Race ' + raceNumber).
                    attr({
                        id: 'result' + raceNumber,
                        onclick: 'populateHorses(' + raceNumber + ')'
                    }).attr('data-title', 'Race ' + raceNumber + ' Results').
                    attr('data-button-primary-action', 'enterresultForRace(' + raceNumber + ')');
                    $('#' + cId + ' div.card-body a#open0').attr({
                        id: 'open' + raceNumber,
                        onclick: 'openWindow(' + raceNumber + ')'
                    });

                    const cardId = 'card' + raceNumber;
                    $('#' + collapseId + ' div#card0').attr('id', cardId);
                    $('#' + cardId + ' div.d-flex a#deleteRace0').text('Delete Race ' + raceNumber).attr({
                        id: 'deleteRace' + raceNumber,
                        href: ''
                    }).attr('data-title', 'Delete Race ' + raceNumber).
                    attr('data-message', 'Are you sure you want to delete Race ' + raceNumber + ' ?').
                    attr('data-button-primary-action', 'deleteRace(' + raceNumber + ')');

                    $('#' + cardId + ' div select#0').attr('id', raceNumber);

                    const addInputId = 'addInput' + raceNumber;
                    $('#' + cardId + ' div#addInput0').attr('id', addInputId);
                    $('#' + addInputId + ' div#horse0').attr('id', 'horse' + raceNumber);
                    $('#horse' + raceNumber + ' input#id0').attr({
                        id: 'id' + raceNumber,
                        name: 'horses[' + raceNumber +'][0]'
                    });
                    $('#horse' + raceNumber + ' span#00').attr({
                        id: raceNumber + '0',
                        onclick: "deleteHorse('horse" +raceNumber + "', '" + raceNumber + "0')"
                    });

                    $('#' + cardId + ' div span#addHorse0').attr({
                        id: 'addHorse' + raceNumber,
                        onclick: "addHorse('addHorse" + raceNumber +"')"
                    });

                    $('#' + cardId + ' div a#update0').text('Save Race ' + raceNumber).
                    attr('id', 'update' + raceNumber).
                    attr('data-title', 'Save Changes for Race' + raceNumber).
                    attr('data-message', 'Are you sure you want to update Race ' + raceNumber + ' ?').
                    attr('data-button-primary-action', 'updateRace(' + eventNumber + ', ' + raceNumber + ')').
                    attr('data-button-secondary-action', 'dismiss(' + raceNumber + ')');

                    $('#' + cardId + ' div a#wind0').attr({
                        id: 'wind' + raceNumber,
                        onclick: "closeWindow(" + raceNumber + ")"
                    });

                    // Re-bind
                    updateNumberOfHorsesInputValue();
                    bindOnChangeOnSelectMenu();
                    bindOnClickCancelRace();

                }
            }
        });

    }


    $( document ).ready( function () {
        $('.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

        updateNumberOfHorsesInputValue();
        bindOnChangeOnSelectMenu();
        bindOnClickCancelRace();

    });



    // Done
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

    function editPot() {
        $.ajax({
            type: 'POST',
            url: './race.php?e='+ eventNumber +'&r=' + 1 + '&q=' + 8,
            data: {pot: $('#jackpotEdit').val() },
            dataType: 'json',
            success: function (data) {
                $('main').prepend(data['alert']);
                $('#alert').delay( 3000 ).fadeOut( 400 );

                if (data['edited'] === 1) {
                    $('#pot').val(data['pot']);
                    $('#jackpotEdit').val(data['pot']);
                }
            }
        });
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
                    <input type="text" class="form-control" name="pot" id="pot" aria-label="Amount (to the nearest dollar)" value="<?php echo $event_pot ?>" readonly>
                    <div class="input-group-append">
                        <a href="#" class="btn input-group-text <?php echo $jackpot_btn_none?>"
                           data-toggle="modal"
                           data-target="#mainModal"
                           data-title="Edit Jackpot"
                           data-message="
                            <div class='input-group input-group-sm mb-3'>
                                <div class='input-group-prepend'>
                                    <span class='input-group-text'>$</span>
                                </div>
                                <input type='text' id='jackpotEdit' class='form-control' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-sm' value='<?php echo $event_pot ?>'>
                            </div>"
                           data-button-primary-text="Save"
                           data-button-primary-action="editPot()"
                           data-button-secondary-text="Cancel"
                           data-button-secondary-action=""
                        >
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <fieldset class="accordion border border-dark" id="accordion01">
                <legend class="text-center w-auto">Races</legend>

                <!--- Race HTML To clone-->
                <div class="group border-bottom border-dark d-none" id="group0">
                    <div class="d-flex flex-row">
                        <button id="btn0" class="btn btn-block dropdown-toggle dt" type="button"
                                data-toggle="collapse" data-target="#collapse0" aria-expanded="true"
                                aria-controls="collapseOne">
                            Race 0
                        </button>
                        <a href="" class="btn btn-outline-primary mb-1"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-in-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8.146 11.354a.5.5 0 0 1 0-.708L10.793 8 8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0z"/>
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 1 8z"/>
                                <path fill-rule="evenodd" d="M13.5 14.5A1.5 1.5 0 0 0 15 13V3a1.5 1.5 0 0 0-1.5-1.5h-8A1.5 1.5 0 0 0 4 3v1.5a.5.5 0 0 0 1 0V3a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v10a.5.5 0 0 1-.5.5h-8A.5.5 0 0 1 5 13v-1.5a.5.5 0 0 0-1 0V13a1.5 1.5 0 0 0 1.5 1.5h8z"/>
                            </svg></a>
                    </div>
                    <div id="collapse0" class="collapse race" data-parent="#accordion01">
                        <div class="text-center card-body  d-none" id="c0">
                            <h4>The betting window has closed.</h4>
                            <span></span>
                            <div class="custom-control custom-checkbox mt-4">
                                <input type="checkbox" class="custom-control-input cancel-race" id="cancel0" >
                                <label class="custom-control-label" for="cancel0">Cancel Race 0</label>
                            </div>
                            <div class="text-center card-body px-3">
                                <a href="#" class="btn btn-primary  close-btn" id="result0"
                                   data-toggle="modal"
                                   data-target="#mainModal"
                                   data-title="Race 0 Results"
                                   data-button-primary-text="Save"
                                   data-button-primary-action="enterResultForRace(0, 0)"
                                   data-button-secondary-text="Exit"
                                   data-button-secondary-action="depopulateHorses()"
                                   onclick="populateHorses(0)"
                                >Enter Results for Race 0</a>
                                <a href="#" class="btn btn-secondary  close-btn" id="open0"
                                   onclick="openWindow(0)">Reopen Betting Window</a>
                            </div>
                        </div>
                        <div class="card-body" id="card0">
                            <div class="d-flex flex-row-reverse mb-2">
                                <a href="#" id="deleteRace0" class="btn btn-outline-danger del-group"
                                   data-toggle="modal"
                                   data-target="#mainModal"
                                   data-title="Delete Race 0"
                                   data-message="Are you sure you want to delete Race 0?"
                                   data-button-primary-text="Confirm"
                                   data-button-primary-action="deleteRace('0')"
                                   data-button-secondary-text="Cancel"
                                   data-button-secondary-action=""
                                >Delete Race 0</a>
                            </div>
                            <div class="form-row">
                                <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                <select id="0" class="custom-select form-control col-sm-10 group-select" required>
                                    <?php
                                        // Horse count
                                        $horse_count = isset($_SESSION["site_default_horse_count"]) ?
                                            $_SESSION["site_default_horse_count"] : 1;

                                        for ($i = 1; $i < $horse_count + 1; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div id="addInput0" class="form-row mt-4 addSelect">
                                <div class="input-group mb-1 group-horse" id="horse0">
                                    <input type="text" id="id0" name="horses[0][0]"
                                           class="custom-select my-1 mr-sm-2"
                                           value="">
                                    <div class="input-group-append">
                                        <span class="btn btn-danger " id="00" onclick="deleteHorse('horse0', '00')"
                                         style="border-radius: 100px">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <span class="btn btn-success " id="addHorse0" style="border-radius: 100px"
                                onclick="addHorse('addHorse0')">+</span>
                                <a href="#" class="btn btn-primary" id="update0"
                                   data-toggle="modal"
                                   data-target="#mainModal"
                                   data-title="Save Changes for Race 0"
                                   data-message="Are you sure you want to update Race 0?"
                                   data-button-primary-text="Confirm"
                                   data-button-primary-action="updateRace(0, 0)"
                                   data-button-secondary-text="Cancel"
                                   data-button-secondary-action="dismiss(0)"
                                >Save Race 0</a>
                                <a href="#" class="btn btn-primary" id="wind0">Close betting window</a>
                            </div>
                        </div>
                    </div>
                </div> <!--- Race HTML To clone-->

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
                        <a href="/races/?e=$event_id&r=$race_num" class="btn btn-outline-primary mb-1"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-in-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
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
                                            data-button-primary-action="enterResultForRace($race_num)" 
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
                                        data-button-primary-action="deleteRace('$race_num')" 
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
                                                            data-button-primary-action="updateRace($race_num)" 
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

                                    if (!empty($finish[$race_num - 1][$k])) {
                                        $race_HTML .= <<< HTML
                                                <script>
                                                        $( document ).ready(function() {
                                                            resultWereEnteredForRace($race_num);
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
                <a href="#" id="addRace" class="btn btn-primary <?php echo $disabled_add_race_button ?>" onclick="addRace()"> Add a Race </a>
            </div>
<!--            <div class="text-center mt-3">-->
<!--                <input type="submit" id="event" name="update_event" value="Update Event" class="btn btn-primary d-none">-->
<!--            </div>-->
        </form>
    </section>
</main>

{footer}
<?php
    ob_end_flush();
?>
