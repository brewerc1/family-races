<?php

// Refactoring in Progress

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');
session_start();

// set the page title for the template
$page_title = "Events";

// Check login state
if(empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif($_SESSION["admin"] != 1) { // Only allow admin
    header("Location: /races/");
    exit;
}

$debug = debug();

$current = '';
$prior = '';
$status = '';

$query = "SELECT id, name, status FROM event ORDER BY id DESC";
$events = $pdo->prepare($query);
if ($events->execute()) {
    if ($events->rowCount() > 0) {
        $row = $events->fetchAll();
        $index = 0;
        while ($index < count($row)) {
            $event_id = $row[$index]["id"];
            $event_name = $row[$index]["name"];
            $event_status = $row[$index]["status"];

            if ($event_status != 1) {
				$current = "<a class='btn btn-primary mb-3' href='./manage.php?e=$event_id'>Manage $event_name</a>";
            } else {
                $prior .= "<li class='list-group-item'>$event_name <span class='badge badge-success badge-pill float-right px-2 completed_badge'>completed</span></li>";
            }
            $index++;
        }
    } else {
        $current = '<p class="alert alert-info" role="alert">No events have been created.</p>';
    }
} else {
    $current = '<p class="alert alert-danger" role="alert">Something went wrong. <span>Please log out and log back in.</span></p>';
}

?>

{header}
{main_nav}

    <main role="main" id="admin_events_page">
        <h1 class="mb-5 sticky-top">Events</h1>
		<section id="current_event" class="mt-5">
			<h2>Current Event</h2>
			<div class="text-center mt-3">
				<?php
				echo $current;
				if(!empty($current)){
					echo <<< BUTTON
					<a class="btn btn-primary mb-4 text-center" href="./create.php">Create New Event</a>
					<p><small><mark><b>Developer note:</b> After QA, we need to change the conditional for this block to be empty() instead of !empty(). I've left this button shown for QA purposes. We need to consider what action sets event.status in the DB.</mark></small></p>
BUTTON;
				}
				?>
			</div>
		</section>
<?php
		if(!empty($prior)) { // we have prior events
echo <<< PRIOR
		<section id="previous_events" class="mt-5">
            <h2>Previous Events</h2>
                <ul class="list-group list-group-flush mt-3">
                    $prior
                </ul>
		</section>
PRIOR;
		}
?>
    </main>

{footer}
<?php ob_end_flush(); ?>
