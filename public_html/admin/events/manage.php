<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

$page_title = "Create Event";

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


?>
{header}
{main_nav}

<main role="main">
    <section>
        <h1>Create an Events</h1>

        <div class="text-center">
            <h2>Reunion 2022</h2>
            <span>November 10, 2022</span>
        </div>

        <fieldset class="accordion border border-dark" id="accordionExample">
            <legend class="text-center w-auto">Races</legend>
                <div>
                    <button id="headingOne" class="btn btn-block dropdown-toggle" type="button" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapseOne">
                            Race 1
                    </button>
                    <div id="collapse1" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-row">
                                    <label>Number of horses:</label>
                                    <select class="custom-select" name="horse_num" required>
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </fieldset>

    </section>
</main>

{footer}
<?php ob_end_flush(); ?>
