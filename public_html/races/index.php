<?php
/**
 * Page to display Races
 * 
 * This page displays races of the current event.
 * Logged in users can place bets and view results.
 * User data for logged in user is stored in $_SESSION['email']
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
if (!empty($_GET['e']) && is_numeric($_GET['e'])) {
    $event = $_GET['e'];
}
else {
    $event = $_SESSION['current_event'];
}

// Handle Race TODO: impliment $_SESSION['current_race']

if (!empty($_GET['r']) && is_numeric($_GET['r'])){
    $race = $_GET['r'];
}
elseif (!empty($_GET['r']) && $_GET['r'] == 'all') {
    $race = 0;
}
else {
    $current_race_sql = "SELECT * FROM `race` WHERE race.event_id = :event AND race.window_closed = 0 LIMIT 1";
    $current_race_result = $pdo->prepare($current_race_sql);
    $current_race_result->execute(['event' => $event]);
    $current_race = $current_race_result->fetch();

    $race = $current_race['race_number'];
}

// Handle if user only selected one thing and clicked submit
if (!empty($_GET['p']) && is_numeric($_GET['p'])) {
    $old_pick = $_GET['p'];
}
if (!empty($_GET['f']) && is_string($_GET['f'])) {
    $old_finish = $_GET['f'];
}

if($_SESSION['site_memorial_race_enable'] == '1'){
    $memorial_race_number = $_SESSION['site_memorial_race_number'];
}

///// DEBUG
//$debug = debug();
///// end DEBUG

// Gather data for this page
// SQL to retrieve race results
$race_sql = 'SELECT user.first_name, user.last_name, user.photo, user.update_time, race_standings.race_event_id, race_standings.race_race_number, race_standings.user_id, race_standings.earnings, event.name, event.date 
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

// SQL to get the horses for each race
$horses_sql = "SELECT * FROM `horse` WHERE horse.race_event_id = :event AND horse.race_race_number = :race";
$horses_result = $pdo->prepare($horses_sql);
$horses_result->execute(['event' => $event, 'race' => $race]);
$horse = $horses_result->fetch();
$horses_count = $horses_result->rowCount();

// SQL to get information about each race
$race_info_sql = "SELECT * FROM `race` WHERE race.event_id = :event AND race.race_number = :race";
$race_info_result = $pdo->prepare($race_info_sql);
$race_info_result->execute(['event' => $event, 'race' => $race]);
$race_info = $race_info_result->fetch();

// SQL to get the race standings (used for the purse)
$race_standings_sql = "SELECT * FROM `race_standings` WHERE race_standings.race_event_id = :event AND race_standings.race_race_number = :race AND race_standings.user_id = :user_id LIMIT 1";
$race_standings_result = $pdo->prepare($race_standings_sql);
$race_standings_result->execute(['event' => $event, 'race' => $race, 'user_id' => $uid]);
$race_standings_info = $race_standings_result->fetch();

// SQL to get the event standings
// SELECT user_id, SUM(earnings) total FROM `race_standings` WHERE race_standings.race_event_id = 1 GROUP BY race_standings.user_id ORDER BY total DESC
// SELECT user.first_name, user.last_name, user_id, SUM(earnings) total FROM `race_standings` INNER JOIN `user` ON user.id=race_standings.user_id WHERE race_standings.race_event_id = 1 GROUP BY race_standings.user_id ORDER BY total DESC
$event_standings_sql = "SELECT user.first_name, user.last_name, user.photo, user.update_time, user_id, SUM(earnings) total FROM `race_standings` INNER JOIN `user` ON user.id=race_standings.user_id WHERE race_standings.race_event_id = :event GROUP BY race_standings.user_id ORDER BY total DESC";
$event_standings_result = $pdo->prepare($event_standings_sql);
$event_standings_result->execute(['event' => $event]);
$user_count = $event_standings_result->rowCount();

// SQL Queries to get the win, place, show horses
$win_horse_sql = "SELECT * FROM `horse` WHERE horse.race_event_id = :event AND horse.race_race_number = :race AND horse.finish = 'win' LIMIT 1";
$win_horse_result = $pdo->prepare($win_horse_sql);
$win_horse_result->execute(['event' => $event, 'race' => $race]);
$win_horse = $win_horse_result->fetch();

$place_horse_sql = "SELECT * FROM `horse` WHERE horse.race_event_id = :event AND horse.race_race_number = :race AND horse.finish = 'place' LIMIT 1";
$place_horse_result = $pdo->prepare($place_horse_sql);
$place_horse_result->execute(['event' => $event, 'race' => $race]);
$place_horse = $place_horse_result->fetch();

$show_horse_sql = "SELECT * FROM `horse` WHERE horse.race_event_id = :event AND horse.race_race_number = :race AND horse.finish = 'show' LIMIT 1";
$show_horse_result = $pdo->prepare($show_horse_sql);
$show_horse_result->execute(['event' => $event, 'race' => $race]);
$show_horse = $show_horse_result->fetch();

$background_image = random_photo();

$memorial_race_content = '';
if ($memorial_race_number == $race) {
    $memorial_race_content =<<< MEMORIAL
    <div id="memorial_race_content"><img src="https://fakeimg.pl/640x360"></div>
MEMORIAL;
}

?>
{header}
{main_nav}
<main role="main" id="races_page">
	<div class="card sticky-top" id="pick_block" style="background-image: url(<?php echo $background_image['filename'];?>);">
		<div class='card-blur'></div>
        <div class="input-group input-group-lg mb-3 pt-2 pl-2 pr-2">
            <div class="input-group-prepend">
                <label class="input-group-text" for="race_picker">Race</label>
			</div>
            <select class="custom-select" id="race_picker">
                <?php
                    for($i = 1; $i <= $num_races; $i++) {
                        if ($i == $race) {
                            if ($memorial_race_number == $i) {
                                echo "<option value='e=$event&r=$i&u=$uid' selected disabled>" . $_SESSION['site_memorial_race_name'] . "</option>";
                            }
                            else {
                                echo "<option value='e=$event&r=$i&u=$uid' selected disabled>Race $i</option>";
                            }
                        }
                        else {
                            if ($memorial_race_number == $i) {
                                echo "<option value='e=$event&r=$i&u=$uid'>" . $_SESSION['site_memorial_race_name'] . "</option>";
                            }
                            else {
                                echo "<option value='e=$event&r=$i&u=$uid'>Race $i</option>";
                            }
                        }
                    }
                    if ($race == 0) {
                        echo "<option value='e=$event&r=all&u=$uid' selected disabled>All Races</option>";
                    }
                    else {
                        echo "<option value='e=$event&r=all&u=$uid'>All Races</option>";
                    }
                ?>
            </select>
		</div>
		<?php echo $memorial_race_content;?>
        <?php if ($race_info) { // Checks to see if there is race info for the race (used to check 'All Races')
            if($race_info['window_closed'] == '0') {?>
                <form action="bets.php" method="POST">
                    <div class="card-body">
                        <div class="form-group input-group input-group-lg mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="horseSelection">Pick</label>
                            </div>
                            <select class="custom-select" id="horseSelection" name="horseSelection" required>
                                <?php
                                    // If no horse has been selected, default to 'Horse'
                                    echo "<option value='default' selected disabled>Horse...</option>";
                                    for($i = 0; $i < $horses_count; $i++){
                                        if (($horse['horse_number'] == $pick['horse_number']) || ($horse['horse_number'] == $old_pick)) {
                                            echo "<option selected>" . $horse['horse_number']. "</option>";
                                        }
                                        else {
                                            echo "<option>" . $horse['horse_number'] . "</option>";
                                        }
                                        $horse = $horses_result->fetch();
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group input-group input-group-lg mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="placeSelection">Finish</label>
                            </div>
                            <select class="custom-select" id="placeSelection" name="placeSelection" required>
                                <!-- If no finish has been selected, default to 'Choose' -->
                                <option value="default" selected disabled>Choose...</option>
                                <?php 
                                if (($pick['finish'] == 'win') || ($old_finish == 'win')) {
                                    echo "<option value='win' selected>Win</option>";
                                }
                                else {
                                    echo "<option value='win'>Win</option>";
                                }
                                if (($pick['finish'] == 'place') || ($old_finish == 'place')) {
                                    echo "<option value='place' selected>Place</option>";
                                }
                                else {
                                    echo "<option value='place'>Place</option>";
                                }
                                if (($pick['finish'] == 'show') || ($old_finish == 'show')) {
                                    echo "<option value='show' selected>Show</option>";
                                }
                                else {
                                    echo "<option value='show'>Show</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" value=<?php echo "$race"; ?> name="currentRace" id="currentRace">
                        <input class="btn btn-primary" type="submit" value="Submit">
                    </div>
                </form>
        <?php } 
        elseif ($race_info['window_closed'] == '1') {
			?>
            <div class="card-body" id="window_closed">
				<div class="card-text" id="no_pick">
                <?php 
                    if($pick){ ?>
                        <h1>You picked horse #<?php echo "<span class='horse-number animate__animated animate__bounceIn'>{$pick['horse_number']}</span> to <span class='horse-finish animate__animated animate__bounceIn'>" . ucfirst($pick['finish']) . "</span>";?></h1>
                        <?php if ($race_standings_info) {
                            echo "<h3>Your purse: $" . $race_standings_info['earnings'] . "</h3>";
						}
						if (!$win_horse) {
							echo "<h3>Awaiting race results</h3>";
						}
						?>
                    </div>
                <?php } else { ?>
                        <h1>You didn't make a pick</h1>
                        <h3>Purse: $0.00</h3>
				<?php } ?>
					</div>
				<?php
                    if ($win_horse) {
                        echo <<< HERE
                    <table id="scoreboard">
                        <tr id="title_row">
                            <td colspan="5"><img src="/images/kc-logo-white.svg" alt="Keene Challenge logo"> Keene Challenge</td>
                        </tr>
                        <tr id="first">
                            <th>1st</th>
                            <td>{$win_horse['horse_number']}</td>
                            <td>&#36;{$win_horse['win_purse']}</td>
                            <td>&#36;{$win_horse['place_purse']}</td>
                            <td>&#36;{$win_horse['show_purse']}</td>
                        </tr>
                        <tr id="second">
                            <th>2nd</th>
                            <td>{$place_horse['horse_number']}</td>
                            <td></td>
                            <td>&#36;{$place_horse['place_purse']}</td>
                            <td>&#36;{$place_horse['show_purse']}</td>
                        </tr>
                        <tr id="third">
                            <th>3rd</th>
                            <td>{$show_horse['horse_number']}</td>
                            <td></td>
                            <td></td>
                            <td>&#36;{$show_horse['show_purse']}</td>
                        </tr>
                    </table>
HERE;

                    }
                } 
                else { // SHOULD NEVER REACH HERE!!!
                echo "<h1> ERROR! </h1>";
                }
            }
        else { // Comes here when showing 'All Races'
            echo "<div class='card-body' id='window_closed'><h1 class='card-title'>Current Event Leaderboard</h1></div>";
        }?>
        </div>
    </div>
    
	<ul class="user-list list-group list-group-flush" id="race_leaderboard">
<?php
if ($race == 0) { // Case when we are displaying the entire leaderboard
    while($row = $event_standings_result->fetch()) {
        $name = $row["first_name"] . ' ' . $row["last_name"];
        $update_time_stamp = strtotime($row["update_time"]); // convert to timestamp for cache-busting
        
        if(empty($row["photo"])) { // Handle missing profile photo
            $photo = "/images/no-user-image.jpg"; // do not cache-bust this image
        }else{
            $photo = $row["photo"] . "?$update_time_stamp"; // cache-bust this image
        }
        echo <<< HERE
    <li class="list-group-item">
        <div class="media">
            <a href="/user/?u={$row["user_id"]}">
                <img src="$photo" alt="A photo of $name" class="rounded-circle">
            </a>
            <div class="media-body"><span class="user_name d-inline-block px-3">$name</span> <span class="earnings badge badge-success float-right px-2">\${$row["total"]}</span></div>
        </div>
    </li>
HERE;
    }
}
else { // Other cases when individual race results are being shown
    if ($num_race_results > 0) {
        $invited = "";

        // Output data of each row
        while($row = $race_result->fetch()) {
            $name = $row["first_name"] . ' ' . $row["last_name"];
            $update_time_stamp = strtotime($row["update_time"]); // convert to timestamp for cache-busting
            
            if(empty($row["photo"])) { // Handle missing profile photo
                $photo = "/images/no-user-image.jpg"; // do not cache-bust this image
            }else{
                $photo = $row["photo"] . "?$update_time_stamp"; // cache-bust this image
            }
            echo <<< HERE
        <li class="list-group-item">
            <div class="media">
                <a href="/user/?u={$row["user_id"]}">
                    <img src="$photo" alt="A photo of $name" class="rounded-circle">
                </a>
                <div class="media-body"><span class="user_name d-inline-block px-3">$name</span> <span class="earnings badge badge-success float-right px-2">\${$row["earnings"]}</span></div>
            </div>
        </li>
HERE;
        }
    } else {
        if ($race_info['window_closed'] == '0') { // Don't show any text when the window is still open

    }
    else { // Show there is no results entered in yet
        echo <<< NORESULTS
            <div class="no-results">
            </div>
NORESULTS;
        }
}
}
?>
    </ul> <!-- END id race_leaderboard -->
</main>
{footer}
<?php ob_end_flush(); ?>