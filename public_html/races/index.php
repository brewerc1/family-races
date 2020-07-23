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

// Handle Race TODO: impliment $_SESSION['current_race']
if(isset($_GET['r']) && is_numeric($_GET['r'])){
    $race = $_GET['r'];
}else{
    $current_race_sql = "SELECT * FROM `race` WHERE race.event_id = :event AND race.window_closed = 0 LIMIT 1";
    $current_race_result = $pdo->prepare($current_race_sql);
    $current_race_result->execute(['event' => $event]);
    $current_race = $current_race_result->fetch();

    $race = $current_race['race_number'];
}

// Handle if user only selected one thing and clicked submit
if (isset($_GET['p']) && is_numeric($_GET['p'])) {
    $old_pick = $_GET['p'];
}
if (isset($_GET['f']) && is_string($_GET['f'])) {
    $old_finish = $_GET['f'];
}

if($_SESSION['site_memorial_race_enable'] == '1'){
    $memorial_race = '';
}

///// DEBUG
$debug = debug("UID: $uid<br>Event: $event");
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

$race_standings_sql = "SELECT * FROM `race_standings` WHERE race_standings.race_event_id = :event AND race_standings.race_race_number = :race AND race_standings.user_id = :user_id LIMIT 1";
$race_standings_result = $pdo->prepare($race_standings_sql);
$race_standings_result->execute(['event' => $event, 'race' => $race, 'user_id' => $uid]);
$race_standings_info = $race_standings_result->fetch();

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
        <?php if($race_info['window_closed'] == '0') {?>
            <form action="bets.php" method="POST">
                <div class="card-body">
                    <?php // TODO: Conditional statements to check if window closed (display results)
                        // TODO: Conditional statement to check if window not open yet
                        /* CONSIDER: If window is closed, perhaps the best way to show the user's bet is to use the select menu choices
                        set as readonly plain text as described here: https://getbootstrap.com/docs/4.0/components/forms/#readonly-plain-text
                        */
                    ?>
                    <div class="form-group input-group input-group-lg mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="horseSelection">Pick</label>
                        </div>
                        <select class="custom-select" id="horseSelection" name="horseSelection" required>
                            <?php // TODO: Select selected horse on navigation.
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
        <?php } elseif ($race_info['window_closed'] == '1') {?>
        <div>
            <h1>The window for this race has been closed!</h1>
            <?php if($pick){ ?>
                <h2>Your Bet: <?php echo "{$pick['horse_number']} to {$pick['finish']}";?></h2>
            <?php } else { ?>
                <h2>No Bet Logged!</h2>
            <?php } ?>
        </div>
        <?php } else {?>
            <h1> in else </h1>
        <?php } ?>
    </div>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="race">
        <!-- Race Select Menu -->
        
    </form> <!-- END id race_picker -->
    <?php if ($race_standings_info) {
        echo "<strong>Purse: {$race_standings_info['earnings']}</strong>";
    }?>
    
	<ul class="user-list list-group list-group-flush" id="race_leaderboard">
<?php
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
    echo "<p>0 results</p>";
}
?>
    </ul> <!-- END id race_leaderboard -->
</main>
{footer}
<?php ob_end_flush(); ?>