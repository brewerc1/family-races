<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

$page_title = "Create Event";

// Check login state
if(empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif($_SESSION["admin"] != 1) { // Only allow admin
    header("Location: /races/");
    exit;
}

//$debug = debug();

if (isset($_POST["submit"])) {
	// convert the date string provided by bootstrap-datepicker into a timestamp, then to YYYY-MM-DD format for MySQL
	$date = date("Y-m-d", strtotime($_POST["event_date"]));
    try {

        $sql = "INSERT INTO event (name, date, pot) VALUES (:name, :date, :pot)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute(['name' => $_POST["event_name"],
            'date' => $date, 'pot' => $_POST["event_pot"]]);

        $sql = "SELECT id FROM event WHERE name=:name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $_POST["event_name"]]);
        $event_id = $stmt->fetch()["id"];
        $_SESSION['current_event'] = $event_id;


        $sql = "INSERT INTO race (event_id, race_number) VALUES (:event_id, :race_number)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute(['event_id' => $event_id,
            'race_number' => 1]);


        header("Location: ./manage.php?e=$event_id");
        exit;

    } catch (Exception $e) {
        header("Location: ./?m=6&s=warning");
        exit;
    }
}


?>
{header}
{main_nav}

    <main role="main" id="admin_event_create_page">
		<h1 class="mb-5 sticky-top">Create an Event</h1>

			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
				<section>
	                <!-- Event Name -->
	                <div class="form-row">
	                    <div class="form-group col-md-6">
							<label for="name" class="col-form-label">Event Name:</label>
							<div class="input-group">
								<div class="input-group-prepend">
									 <span class="input-group-text"><i class="far fa-horse-head"></i></span>
  								</div>
								  <input type="text" class="form-control" id="name" name="event_name">
							</div>
	                    </div>
	                	<!-- Event Date -->
						<div class="form-group col-md-6">
							<label for="date" class="col-form-label">Event Date:</label>
							<div class="input-group date" data-provide="datepicker" data-date-format="DD, MM d, yyyy" data-date-auto-close="true" data-date-today-highlight="true" data-date-orientation="auto"  data-date-z-index-offset="2000" data-date-clear-btn="true" data-date-today-btn="true" >
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
								</div>
								<input type="text" class="form-control" id="date" name="event_date">
							</div>
						</div>
					</div>
	                <!-- Event POT -->
	                <div class="form-row">
	                    <div class="form-group col-md-6">
							<label for="pot" class="col-form-label">Pot:</label>
							<div class="input-group">
								<div class="input-group-prepend">
									 <span class="input-group-text"><i class="far fa-donate"></i></span>
  								</div>
								<input type="text" class="form-control" id="pot" name="event_pot">
							</div>
	                    </div>
	                </div>
				</section>

				<div class="form-row my-5">
					<div class="col text-center">
						<!--<input type="submit" name="submit" class="btn btn-primary btn-block" value="Next">-->
	                    <button type="submit" class="btn btn-primary btn col-sm-5" name="submit">Next</button>
	                    <a class="btn btn-text d-block mt-2 text-center" href="/admin/events/">Cancel</a>
	                </div>
				</div>

            </form>

    </main>

{footer}
<?php ob_end_flush(); ?>
