<?php
/**
 * Page to display Races
 * 
 * This page displays races of the current event.
 * Logged in users can place bets and view results.
 * User data for logged in user is stored in $_SESSION.email
 * Page checks for $_GET
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Test for authorized user
if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    exit;
} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    exit;
}

// Set the page title for the template
$page_title = "Races";

// Include the race picker javascript
$javascript = <<< JAVASCRIPT
$(function(){
// bind change event to select
    $('#race_picker').on('change', function () {
        var url = "/races/?" + $(this).val(); // get selected value
        if (url) { // require a URL
            window.location = url; // redirect
        }
    return false;
    });
});
JAVASCRIPT;

// Get UID
$uid = $_SESSION['id'];

// URL needs to have the GET variables to work ex: http://localhost/races/?e=1&r=3
// Handle Event
if(isset($_GET['e']) && is_numeric($_GET['e'])){
    $event = $_GET['e'];
}else{
    $event = $_SESSION['current_event'];
}

// Handle Race
if(isset($_GET['r']) && is_numeric($_GET['r'])){
    $race = $_GET['r'];
}else{
    $race = 1;
}

if($_SESSION['site_memorial_race_enable'] == '1'){
    $memorial_race = '';
}

///// DEBUG
$debug = debug("UID: $uid<br>Event: $event");
///// end DEBUG

// Gather data for this page
// SQL to retrieve race results
$race_sql = 'SELECT user.first_name, user.last_name, user.photo, race_standings.race_event_id, race_standings.race_race_number, race_standings.user_id, race_standings.earnings, event.name, event.date 
FROM race_standings 
INNER JOIN event ON event.id = race_standings.race_event_id 
INNER JOIN user ON user.id = race_standings.user_id 
WHERE race_standings.race_event_id = :event AND race_standings.race_race_number = :race 
ORDER BY race_standings.race_event_id, race_standings.race_race_number, race_standings.earnings DESC';
$race_result = $pdo->prepare($race_sql);
$race_result->execute(['event' => $event, 'race' => $race]);
$num_race_results = $race_result->rowCount();

// SQL to determine this user's pick for this race
$pick_sql = "SELECT * FROM `pick` WHERE pick.user_id = :user_id AND pick.race_event_id = :event AND pick.race_race_number = :race LIMIT 1";
$pick_result = $pdo->prepare($pick_sql);
$pick_result->execute(['user_id' => $uid, 'event' => $event, 'race' => $race]);
$pick = $pick_result->fetch();

// SQL to calculate number of races in this event
$num_races_sql = "SELECT * FROM `race` WHERE race.event_id = :event";
$num_races_result = $pdo->prepare($num_races_sql);
$num_races_result->execute(['event' => $event]);
$num_races = $num_races_result->rowCount();

?>
{header}
{main_nav}
<main role="main">

    <div class="card" style=" margin: 0 auto;">
        <div class="input-group input-group-lg mb-3 pt-2 pl-2 pr-2">
            <div class="input-group-prepend">
                <label class="input-group-text" for="race_picker">Race</label>
            </div>
            <select class="custom-select" id="race_picker">
    <?php 
    // Builds the select menu based on number of races
        // TODO: "all" option to display event standings
        // TODO: Replace "race #" display with memorial race title
    for($i = 1; $i <= $num_races; $i++){
        if($i == $race){
            $attr = "selected='selected' disabled='disabled'";
        }else{
            $attr = "";
        }
        echo "<option value='e=$event&r=$i&u=$uid' $attr>Race $i</option>";
    }
    ?>
                <option value="e=$event&r=0&u=$uid">All Races</option>
            </select>
        </div>
        <div class="card-body">
            <?php // TODO: Conditional statements to check if window closed (display results)
// TODO: Conditional statement to check if window not open yet
/* CONSIDER: If window is closed, perhaps the best way to show the user's bet is to use the select menu choices
set as readonly plain text as described here: https://getbootstrap.com/docs/4.0/components/forms/#readonly-plain-text
*/
?>
            <div class="input-group input-group-lg mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">Pick</label>
                </div>
                <select class="custom-select" id="inputGroupSelect01">
                    <?php // TODO: Add the Horse picker. ?>
                    <option selected>Horse...</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="3">Need to show horses in this race! </option>
                </select>
            </div>
            <div class="input-group input-group-lg mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">Finish</label>
                </div>
                <select class="custom-select" id="inputGroupSelect01">
                    <?php // TODO: Add the Place picker. ?>
                    <option selected>Choose...</option>
                    <option value="w">Win</option>
                    <option value="p">Place</option>
                    <option value="s">Show</option>
                </select>
            </div>
            <a href="#" class="btn btn-primary">Do something</a>
        </div>
    </div>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="race">
        <!-- Race Select Menu -->
        
    </form> <!-- END id race_picker -->

	<div id="user_bet"> <!-- Display User's Bet -->
		<!-- TODO: select menu's to display user pick options, defaul to current pick -->
		<p><strong>Your Bet:</strong> <?php echo "{$pick['horse_number']} to {$pick['finish']}";?><br>
		<strong>Purse:</strong> $<?php //echo $purse;?></p>
    </div> <!-- END id user_bet -->
    
	<ul class="user-list list-group list-group-flush" id="race_leaderboard">
<?php
if ($num_race_results > 0) {
    $invited = "";

    // Output data of each row
    while($row = $race_result->fetch()) {
        $name = $row["first_name"] . ' ' . $row["last_name"];
        // Handle missing profile photo
        if(empty($row["photo"])) {
            $photo = "https://races.informatics.plus/images/no-user-image.jpg";
        }else{
            $photo = $row["photo"];
        }
        echo <<< HERE
        <li class="list-group-item">
            <div class="media">
                <a href="/user/?u={$row["user_id"]}">
                    <img src="$photo" alt="photo" class="rounded-circle">
                </a>
                <div class="media-body"><span class="user_name d-inline-block px-3">$name</span> <span class="earnings badge badge-success float-right px-2">\${$row["earnings"]}</span></div>
            </div>
        </li>
HERE;
    }
} else {
    echo "<p>0 results</p>";
}
?>
    </ul> <!-- END id race_leaderboard -->
</main>
{footer}
<?php ob_end_flush(); ?>