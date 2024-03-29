<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Create Event";

$debug = debug();

if(empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} 

if($_SESSION["admin"] != 1) { 
    header("Location: /races/");
    exit;
}

?>

{header}
{main_nav}

    <main role="main" id="admin_event_create_page">
		<h1 class="mb-5 sticky-top">Create New Event</h1>

			<form id="create-event" method="POST">
				<section>
	                <!-- Event Name -->
	                <div class="form-row">
	                    <div class="form-group col-md-6">
							<label for="name" class="col-form-label">Event Name:</label>
							<div class="input-group">
								<div class="input-group-prepend">
									 <span class="input-group-text"><i class="fas fa-horse-head"></i></span>
  								</div>
								  <input type="text" class="form-control" id="name" name="event_name" maxlength="25">
							</div>
	                    </div>
	                	<!-- Event Date -->
						<div class="form-group col-md-6">
							<label for="date" class="col-form-label">Event Date:</label>
							<div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-auto-close="true" data-date-today-highlight="true" data-date-orientation="auto"  data-date-z-index-offset="2000" data-date-clear-btn="true" data-date-today-btn="true" >
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
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
									 <span class="input-group-text"><i class="fas fa-donate"></i></span>
  								</div>
								<input type="number" class="form-control" id="pot" name="event_pot" min="1" max="99999">
							</div>
	                    </div>
	                </div>
				</section>

				<div class="form-row my-5">
					<div class="col text-center">
	                    <a class="btn btn-primary btn col-sm-5 disabled" id="save-event">Save This Event</a>
	                    <a class="btn btn-text d-block mt-2 text-center" href="/admin-v2/events/">Cancel</a>
	                </div>
				</div>

            </form>

    </main>

{footer}
<?php ob_end_flush(); ?>

<script type="text/javascript" src="js/createEvent.js"></script>