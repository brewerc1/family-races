<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

$page_title = "Create Event";
$javascript = "
let numberOfHorsesInputIds = ['01'];
    let groupCount;
    // Clone: Outer element (div.group)
    $('#addRace').on('click', function () {
        groupCount = $('.group').length;
        $('.group:first').clone().appendTo('#accordion01');
        $('.group:last').removeAttr('style');
        $('.dropdown-toggle:last').text('Race ' + groupCount);
        $('.race:last').attr('id', 'collapse' + groupCount);
        $('.dt:last').attr('data-target', '#collapse' + groupCount);
        const id = '0' + groupCount;
        $('.ht:last').attr('name', 'horses['+ (groupCount) +'][]');
        $('.addSInput:last').attr('id', 'addInput' + id);
        $('.hr:last').attr('id', id);
        numberOfHorsesInputIds.push(id);

        if (groupCount > 1) {
            $('#deleteRace').removeClass('disabled');
        }

    });

    // Delete last Race: Outer element (div.group)
    $('#deleteRace').on('click', function () {
        if (groupCount > 1) {
            $('.group:last').remove();
            groupCount--;
            numberOfHorsesInputIds.pop();
        }
        if (groupCount === 1) {
            $('#deleteRace').addClass('disabled');
        }
    });

    // Clone: input element (inner element)
    $('fieldset').on('click', function (e) {
        const idClicked = e.target.id;

        if (numberOfHorsesInputIds.includes(idClicked)) {
            $('#' + idClicked).on('change', function () {
                let numberOfInputElement = $('#addInput' + idClicked + ' input').length;

                // Increment select element
                if (this.value > numberOfInputElement) {
                    for (numberOfInputElement; numberOfInputElement < this.value; numberOfInputElement++) {
                        $('#addInput' + idClicked + ' input:first-of-type').clone().appendTo('#addInput' + idClicked);
                    }
                }

                // Decrement select element
                if (this.value < numberOfInputElement) {
                    const numberOfElementToRemove = numberOfInputElement - this.value;
                    for (let j = 0; j < numberOfElementToRemove; j++) {
                        $('#addInput' + idClicked + ' input:last-of-type').remove();
                    }
                }

            })
        }
    });
";

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
$debug = debug();

?>
{header}
{main_nav}
    <main role="main">
        <section>
            <h1>Create an Event</h1>

            <div class="text-center">
                <h2>Reunion 2022</h2>
                <span>November 10, 2022</span>
            </div>

            <form method="POST" class="mt-3">
                <div class="form-row">
                    <label class="col-sm-2 col-form-label"  for="pot">Jackpot</label>
                    <input type="text" class="form-control col-sm-10" id="pot" value="$ 200">
                </div>

                <fieldset class="accordion border border-dark" id="accordion01">
                    <legend class="text-center w-auto">Races</legend>

                    <!--- Race HTML To Be Cloned -->
                    <div class="group border-top border-dark" style="display: none">
                        <button class="btn btn-block dropdown-toggle dt" type="button" data-toggle="collapse" data-target="#collapse0" aria-expanded="true" aria-controls="collapseOne">
                            Race 0
                        </button>
                        <div id="collapse0" class="collapse race" data-parent="#accordion01">
                            <div class="card-body">
                                <div class="form-row">
                                    <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                    <select id="00" class="custom-select form-control col-sm-10 hr" required>
                                        <?php
                                        for ($i = 1; $i < 10; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="addInput00" class="form-row mt-4 addSInput">
                                    <input type="text" class="custom-select my-1 mr-sm-2 horse ht" placeholder="Horse#">
                                </div>
                            </div>
                        </div>
                    </div> <!---END Race HTML To Be Cloned -->
                    <!--- Race HTML -->
                    <div class="group">
                        <button class="btn btn-block dropdown-toggle dt" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="true" aria-controls="collapseOne">
                            Race 1
                        </button>
                        <div id="collapse" class="collapse show race" data-parent="#accordion01">
                            <div class="card-body">
                                <div class="form-row">
                                    <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                    <select id="01" class="custom-select form-control col-sm-10 hr" required>
                                        <?php

                                        $horse_count = isset($_SESSION["site_default_horse_count"]) ?
                                            $_SESSION["site_default_horse_count"] : 1;

                                        for ($i = 1; $i < $horse_count + 1; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="addInput01" class="form-row mt-4 addSelect">
                                    <input type="text" name="horses[1][]" class="custom-select my-1 mr-sm-2 horse ht" placeholder="Horse#">
                                </div>
                            </div>
                        </div>
                    </div> <!---END Race HTML -->

                </fieldset>
                <div class="text-center mt-4">
                    <a href="#" id="addRace" class="btn btn-primary"> Add a Race </a>
                    <a href="#" id="deleteRace" class="btn btn-danger disabled">Delete a Race</a>
                </div>
                <div class="text-center mt-3">
                    <input type="submit" name="save_event" value="Save Event" class="btn btn-primary">
                </div>
            </form>

        </section>
    </main>
{footer}
<?php ob_end_flush(); ?>
