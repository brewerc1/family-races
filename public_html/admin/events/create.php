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

if (isset($_POST["submit"])) {
    header("Location: /admin/events/manage.php");
    //echo $_POST["event_date"];
}

?>
{header}
{main_nav}

    <main role="main">
        <section>
            <h1>Create an Event</h1>

            <form method="POST" action=<?php echo $_SERVER["PHP_SELF"] ?>>
                <!-- Event Name -->
                <div class="form-group row">
                    <div class="col">
                        <label for="email_from_address" class="col-form-label"> Event Name </label>
                        <input type="text" class="form-control" id="name" name="event_name">
                    </div>
                </div>
                <!-- Event Date -->
                <div class="form-group row">
                    <div class="col">
                        <label for="email_from_address" class="col-form-label"> Date </label>
                        <input type="datetime-local" class="form-control" id="date" name="event_date">
                    </div>
                </div>

                <!-- Event POT -->
                <div class="form-group row">
                    <div class="col">
                        <label for="email_from_address" class="col-form-label"> POT </label>
                        <input type="text" class="form-control" id="pot" name="event_pot">
                    </div>
                </div>

                <!-- submit -->
                <input type="submit" name="submit" class="btn btn-primary btn-block" value="Next">

            </form>
        </section>
    </main>

{footer}
<?php ob_end_flush(); ?>
