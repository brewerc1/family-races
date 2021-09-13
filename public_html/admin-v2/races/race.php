<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

ob_start('template');

$page_title = "Events";

if (empty($_SESSION["id"])) {
	header("Location: /login/");
	exit;
}

if ($_SESSION["admin"] != 1) {
	header("Location: /races/");
	exit;
}

$query = "SELECT memorial_race_enable FROM site_settings";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll();
$toolTipNeeded = $result[0]["memorial_race_enable"] == 1 ? false : true;

// Check if race is currently highlighted
$query = "SELECT memorial_race_number FROM site_settings";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll();
$is_highlighted = $result[0]["memorial_race_number"] == $_GET["r"];

$query = "SELECT id FROM event ORDER BY id DESC LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$is_highlighted = $is_highlighted && $result["id"] == $_GET["e"];

// Highlight race
if (isset($_POST["race"]) && $_SESSION["admin"] == 1) {
	$race = (int) $_POST["race"];
	$query = "UPDATE site_settings SET memorial_race_number=:race";
	$stmt = $pdo->prepare($query);
	$stmt->execute(['race' => $race]);
}

$eventId = (int) $_GET["e"];
$query = "SELECT name, status FROM event WHERE id=:eventId";
$stmt = $pdo->prepare($query);
$stmt->execute(['eventId' => $eventId]);
$data = $stmt->fetch();
$eventName = $data["name"];
$currentEvent = !$data["status"];

?>

{header}
{main_nav}
<main role="main" id="admin_events_page">
	<h1 class="sticky-top padded-top-header pl-4 mb-5" id="manage-race-page-header">
		<a href="../events/" class="font-lighter">Events</a>
		<span class="font-lighter"> > </span>
		<a id="event-name" class="font-lighter"><?php echo $eventName ?></a>
		<span class="font-lighter"> > </span>
		<span id="race-mode"><?php echo $_GET["mode"] . " A Race" ?></span>
	</h1>
	<section id="race" class="mt-5">

		<div id="current-race">
			<h3 class="mb-3">Race <?php echo $_GET["r"] ?></h3>

			<div class="checkbox-container">
				<input type="checkbox" id="highlight-race" data-toggle="tooltip" data-placement="top" <?php echo $is_highlighted ? "Checked" : ""; ?> <?php echo $toolTipNeeded ? "title='Turn on Enable Memorial Race in Site Settings to highlight this race.'" : "title='If a there is already a race being highlighted, it will be replaced.'" ?> <?php echo !$currentEvent ? "disabled" : "" ?>>
				Highlight this race
			</div>

		</div>

		<p id="remove-hint">You may remove any horse that has no bets on it.</p>

		<div id="horses">
		</div>

		<div id="add-horse-container">
			<p><a class="fas fa-plus-circle" style="cursor: pointer;"></a><span>Add a horse</span></p>
		</div>

		<div id="race-done-container">
			<a id="race-done" href="../events/manage.php?e=<?php echo $_GET["e"] . "&pg=" . $_GET["pg"]; ?>" class="black-btn btn">Save</a>
		</div>

		<div id="race-loader">
			<div id="loader-container">
				<div class="lds-ring" id="loader">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</div>
			</div>
		</div>

	</section>
</main>
{footer}
<script type="text/javascript" src="./js/manageRace.js"></script>
<script type="text/javascript" src="./js/highlightRace.js"></script>
<?php ob_end_flush(); ?>