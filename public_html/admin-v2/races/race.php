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

?>

{header}
{main_nav}
<main role="main" id="admin_events_page">
    <h1 class="sticky-top padded-top-header ml-1 mb-5" id="manage-race-page-header">
		<a href="../events/" class="font-lighter">Events</a>
		<span class="font-lighter"> > </span> 
		<a id="event-name" class="font-lighter">Event</a>
        <span class="font-lighter"> > </span> 
        <span id="race-mode"></span>
	</h1>
	<section id="race" class="mt-5">

		<div id="current-race">
			<h3 class="mb-3">Race <?php echo $_GET["r"]?></h3>

            <div class="checkbox-container">
                <input type="checkbox"
					id="highlight-race" data-toggle="tooltip" data-placement="top"
					title="Turn on Enable Memorial Race in Site Settings to highlight this race."
					<?php echo $toolTipNeeded ? "disabled" : ""?>>
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
			<a id="race-done" href="../events/manage.php?e=<?php echo $_GET["e"] . "&pg=" . $_GET["pg"]; ?>" class="black-btn">Save</a>
		</div>

	</section>
</main>
{footer}
<script type="text/javascript" src="./js/manageRace.js"></script>
<?php ob_end_flush(); ?>