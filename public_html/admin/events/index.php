<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Events";

if(empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif($_SESSION["admin"] != 1) {
    header("Location: /races/");
    exit;
}

//$debug = debug();

$output = '';
$has_current_event = 0; // defaults to no current event

try {
	$events_sql = "SELECT id, name, status FROM event ORDER BY id DESC";
	$events_query = $pdo->prepare($events_sql);
	if($events_query->execute()){

		if ($events_query->rowCount() > 0){
			$events_result = $events_query->fetchAll();
			foreach ($events_result as $id => $event_data){

                if ($events_result[$id]['status'] == 0)
                    $has_current_event = 1;

				$output .=  "\t\t\t\t\t<li class='list-group-item'>
						<a href='./manage.php?e={$events_result[$id]['id']}'>{$events_result[$id]['name']} <span class='px-2 status_badge badge badge-pill float-right badge-" . ( $events_result[$id]['status'] == 1 ? "info'>completed" : "success'>current" ) . "</span></a>
					</li>\n";
			}
		} else {
			$output = '<p class="alert alert-info" role="alert">No events have been created.</p>';
		}
	} else {
		$output = '<p class="alert alert-danger" role="alert">Something went wrong. <span>Please log out and log back in.</span></p>';
	}

} catch (Exception $e) {
	header("Location: ./?m=6&s=warning");
	exit;
}
?>
{header}
{main_nav}

    <main role="main" id="admin_events_page">
        <h1 class="mb-5 sticky-top">Events</h1>
		<section id="events" class="mt-5">
			<div class="text-center mb-1 mt-3">
				<a class="btn btn-primary text-center <?php echo $has_current_event == 1 ? 'disabled' : '' ?>" href="./create.php">Create New Event</a>
				<?php echo $has_current_event == 1 ? '<div><small class="text-muted">To create a new event, you must close the current event.</small></div>' : '';?>
			</div>
			<div class="justify-content-center row mt-5">
				<ul class="list-group list-group-flush col-md-6">
<?php echo $output;?>
            	</ul>
			</div>
		</section>
    </main>

{footer}
<?php ob_end_flush(); ?>
