<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

$page_title = "Create Event";

// Check login state
if(empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif($_SESSION["admin"] != 1) { // Only allow admin
    header("Location: /races/");
    exit;
}

$debug = debug();

if (isset($_POST["submit"])) {
    // Create event
    $sql = "INSERT INTO event (name, date, pot) VALUES (:name, :date, :pot)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute(['name' => $_POST["event_name"],
        'date' => $_POST["event_date"], 'pot' => $_POST["event_pot"]]);

    // Get event ID
    $sql = "SELECT id FROM event WHERE name=:name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $_POST["event_name"]]);
    $event_id = $stmt->fetch()["id"];

    // Create the first Race
    $sql = "INSERT INTO race (event_id, race_number) VALUES (:event_id, :race_number)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute(['event_id' => $event_id,
        'race_number' => 1]);

    // Redirect to Manage Event page
    header("Location: ./manage.php?e=$event_id");
    exit;
}


?>
{header}
{main_nav}

    <main role="main" id="admin_event_create_page">
		<h1 class="mb-5 sticky-top">Create an Event</h1>
		<section>

            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
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
                        <input type="date" class="form-control" id="date" name="event_date">
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
