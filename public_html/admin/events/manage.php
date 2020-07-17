<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

$page_title = "Create Event";
$javascript = "

$('#01').on('change', function() {
        for (let i = 1; i < this.value; i++) {
            $('#11').clone().appendTo('#addSelect').attr('id', '1' + (i + 1));
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
                <div class="form-row mt-3">
                    <label class="col-sm-2 col-form-label"  for="url">Video</label>
                    <input type="text" class="form-control col-sm-10" id="url" value="youtu.be/1234123">
                </div>

                <fieldset class="accordion border border-dark" id="accordion01">
                    <legend class="text-center w-auto">Races</legend>
                    <!--- Race HTML -->
                    <div class="group">
                        <button id="headingOne" class="btn btn-block dropdown-toggle" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="true" aria-controls="collapseOne">
                            Race 1
                        </button>
                        <div id="collapse" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion01">
                            <div class="card-body">
                                <div class="form-row">
                                    <label class="col-sm-2 col-form-label"  for="horse_num">Number of horses:</label>
                                    <select id="01" class="custom-select form-control col-sm-10" name="horse_num" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                                <div class="form-row mt-4" id="addSelect">
                                    <select id="11" name="horses[0][]" class="custom-select my-1 mr-sm-2 horse">
                                        <option selected>Horse#</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                                <a href="#" id="btnAdd" class="btn btn-primary btn-block" data-toggle="collapse" data-target="#collapse">Save 1st Race</a>
                                <!---<button type="button" id="btnAdd" class="btn btn-primary" data-toggle="collapse" data-target="#collapse1">Add Employee</button>-->
                            </div>
                        </div>
                    </div> <!---END Race HTML -->

                </fieldset>
                <div class="text-center mt-3">
                    <input type="submit" name="save_event" value="Save Event" class="btn btn-primary">
                </div>
            </form>

        </section>
        <?php var_dump($_POST); ?>
    </main>
<script>
    let i = $('#collapse1').length + 1;

    $('#accordion01').multifield({
        section: '.group',
        btnAdd:'#btnAdd',
        btnRemove:'.btnRemove',
    });
</script>
{footer}
<?php ob_end_flush(); ?>
