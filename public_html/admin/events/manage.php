<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

$page_title = "Create Event";
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
                    <div class="text-center"><a href="#" id="addRace" class="btn btn-primary">Add Race</a></div>
                    <!--- Race HTML To Be Cloned -->
                    <div class="group mt-3" style="display: none">
                        <button class="btn btn-block dropdown-toggle dt" type="button" data-toggle="collapse" data-target="#collapse0" aria-expanded="true" aria-controls="collapseOne">
                            Race 0
                        </button>
                        <div id="collapse0" class="collapse race" data-parent="#accordion01">
                            <div class="card-body">
                                <div class="form-row">
                                    <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                    <select id="00" class="custom-select form-control col-sm-10 hr" name="horse_num" required>
                                        <!--<option value="1">1</option>-->
                                        <!--<option value="2">2</option>-->
                                        <!--<option value="3">3</option>-->
                                        <?php
                                        for ($i = 1; $i < 10; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="addSelect00" class="form-row mt-4 addSelect">
                                    <select id="10" name="horses[0][]" class="custom-select my-1 mr-sm-2 horse ht">
                                        <option selected>Horse#</option>
                                            <!--<option value="1">1</option>-->
                                            <!--<option value="2">2</option>-->
                                            <!--<option value="3">3</option>-->
                                        <?php
                                            for ($i = 1; $i < 201; $i++) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <a href="#" class="btn btn-primary btn-block" data-toggle="collapse" data-target="#collapse0">Save Race 0</a>
                            </div>
                        </div>
                    </div> <!---END Race HTML To Be Cloned -->
                    <!--- Race HTML -->
                    <div class="group mt-3">
                        <button class="btn btn-block dropdown-toggle dt" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="true" aria-controls="collapseOne">
                            Race 1
                        </button>
                        <div id="collapse" class="collapse show race" data-parent="#accordion01">
                            <div class="card-body">
                                <div class="form-row">
                                    <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                    <select id="01" class="custom-select form-control col-sm-10 hr" name="horse_num" required>
                                        <!--<option value="1">1</option>-->
                                        <!--<option value="2">2</option>-->
                                        <!--<option value="3">3</option>-->
                                        <?php
                                        for ($i = 1; $i < 10; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="addSelect01" class="form-row mt-4 addSelect">
                                    <select name="horses[0][]" class="custom-select my-1 mr-sm-2 horse ht">
                                        <option selected>Horse#</option>
                                        <!--<option value="1">1</option>-->
                                        <!--<option value="2">2</option>-->
                                        <!--<option value="3">3</option>-->
                                        <?php
                                        for ($i = 1; $i < 201; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <a href="#" class="btn btn-primary btn-block" data-toggle="collapse" data-target="#collapse">Save Race 1</a>
                            </div>
                        </div>
                    </div> <!---END Race HTML -->

                </fieldset>
                <div class="text-center mt-3">
                    <input type="submit" name="save_event" value="Save Event" class="btn btn-primary">
                </div>
            </form>

        </section>
        <?php print_r($_POST); ?>
    </main>
<script>
    let numberOfHorsesInputIds = ['01'];

    // Clone: Outer element (div.group)
    $('#addRace').on('click', function () {
        let group = $('.group').length;
        $('.group:first').clone().appendTo('#accordion01');
        $('.group:last').removeAttr('style');
        $('.dropdown-toggle:last').text('Race ' + group);
        $('.race:last').attr('id', 'collapse' + group);
        $('.dt:last').attr('data-target', '#collapse' + group);
        $('a.btn:last').text('Save Race ' + group).attr('data-target', '#collapse' + group);
        const id = '01' + group;
        $('.ht:last').attr('name', 'horses['+ (group - 1) +'][]');
        $('.addSelect:last').attr('id', 'addSelect' + id);
        $('.hr:last').attr('id', id);
        numberOfHorsesInputIds.push(id);
    });

    // Clone: Select element (inner element)
    $('body').on('click', function (e) {
        const idClicked = e.target.id;

        if (numberOfHorsesInputIds.includes(idClicked)) {
            $('#' + idClicked).on('change', function () {
                let numberOfSelectElement = $('#addSelect' + idClicked + ' select').length;

                // Increment select element
                if (this.value > numberOfSelectElement) {
                    for (numberOfSelectElement; numberOfSelectElement < this.value; numberOfSelectElement++) {
                        $('#addSelect' + idClicked + ' select:first-of-type').clone().appendTo('#addSelect' + idClicked);
                    }
                }

                // Decrement select element
                if (this.value < numberOfSelectElement) {
                    const numberOfElementToRemove = numberOfSelectElement - this.value;
                    for (let j = 0; j < numberOfElementToRemove; j++) {
                        $('#addSelect' + idClicked + ' select:last-of-type').remove();
                    }
                }

            })
        }
    })
</script>
{footer}
<?php ob_end_flush(); ?>
