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
$debug = debug();

?>
{header}
{main_nav}

    <main role="main">
        <section>
            <h1>Create an Events</h1>

            <form method="POST">
                <!-- Event Name -->
                <div class="form-group row">
                    <div class="col">
                        <label for="email_from_address" class="col-form-label"> Event Name </label>
                        <input type="text" class="form-control" id="e_name" name="name" placeholder="Reunin 2022">
                    </div>
                </div>
                <!-- Event Date -->
                <div class="form-group row">
                    <div class="col">
                        <label for="email_from_address" class="col-form-label"> Date </label>
                        <input type="text" class="form-control" id="date" name="date" placeholder="Date">
                    </div>
                </div>

                <!-- Event POT -->
                <div class="form-group row">
                    <div class="col">
                        <label for="email_from_address" class="col-form-label"> POT </label>
                        <input type="text" class="form-control" id="pot" name="pot" placeholder="Jackpot">
                    </div>
                </div>

                <!-- Event Video URL -->
                <div class="form-group row">
                    <div class="col">
                        <label for="email_from_address" class="col-form-label"> Video URL </label>
                        <input type="text" class="form-control" id="url" name="url" placeholder="youtu.be/1234123">
                    </div>
                </div>

                <!-- Betting window -->
                <div class="form-group custom-control custom-switch custom-switch-lg">
                    <input class="custom-control-input" type="checkbox" id="bet_w" name="bet_w">
                    <label class="custom-control-label" for="bet_w"> Betting windows open sequentially </label>
                </div>

                <!-- submit -->
                <input type="submit" name="submit" class="btn btn-primary btn-block" value="Create Event">

            </form>
        </section>
    </main>

{footer}
<?php ob_end_flush(); ?>
