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
$('#confirm_cancel_button').hide();
$(document).ready(function(){
    $("input:checkbox").change(function() { 
        if($(this).is(":checked")) {
            $('#confirm_cancel_button').show();
            $('#race_results_button').hide();
        } else {
            $('#confirm_cancel_button').hide();
            $('#race_results_button').show();
        }
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

    if ($current_race) {
        $race = $current_race['race_number'];
    }
    else {
        $race = 0;
    }
}

// Handle if user only selected one thing and clicked submit
if (!empty($_GET['p']) && is_numeric($_GET['p'])) {
    $old_pick = $_GET['p'];
}
else {
    $old_pick = -1;
}
if (!empty($_GET['f']) && is_string($_GET['f'])) {
    $old_finish = $_GET['f'];
}
else {
    $old_finish = -1;
}

if($_SESSION['site_memorial_race_enable'] == '1'){
    $memorial_race_number = $_SESSION['site_memorial_race_number'];
}
else {
    $memorial_race_number = -1;
}

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
	<div id="memorial_race_content" class="mt-2"><img src="/uploads/memorial-race.jpg" alt="Memorial photo" class="animate__animated animate__fadeIn"></div>
	<div class='card-blur'></div>
MEMORIAL;
}

$horses = $horses_result->fetchAll();
$horse_list_js = '';
foreach($horses as $key => $value){
	$horse_list_js .= "'{$horses[$key]['horse_number']}',";
}
$horse_list_js = rtrim($horse_list_js, ','); // remove trailing comma

///// DEBUG
//$debug = debug($horses);
///// end DEBUG

?>
{header}
<script>
	let horseList = [<?php echo $horse_list_js;?>];
	function set_horse_val(finish) {
	    let win_horse_number, place_horse_number, show_horse_number;
	    if (finish == "win") {
	        win_horse_number = $('#win-result').val();
	        place_horse_number = $('#place-result').val();
	        show_horse_number = $('#show-result').val();
	        $('#place-result option').removeAttr("disabled")
	        $('#place-result option[value=0]').attr("disabled", "disabled");
	        $('#place-result option[value='+win_horse_number+']').attr("disabled", "disabled");
	        $('#show-result option').removeAttr("disabled")
	        $('#show-result option[value=0]').attr("disabled", "disabled");
	        $('#show-result option[value='+win_horse_number+']').attr("disabled", "disabled");
	        if (place_horse_number != null) {
	            $('#show-result option[value='+place_horse_number+']').attr("disabled", "disabled");
	        }
	        if (show_horse_number != null) {
	            $('#place-result option[value='+show_horse_number+']').attr("disabled", "disabled");
	        }
	    }
	    if (finish == "place") {
	        win_horse_number = $('#win-result').val();
	        place_horse_number = $('#place-result').val();
	        show_horse_number = $('#show-result').val();
	        $('#win-result option').removeAttr("disabled")
	        $('#win-result option[value=0]').attr("disabled", "disabled");
	        $('#win-result option[value='+place_horse_number+']').attr("disabled", "disabled");
	        $('#show-result option').removeAttr("disabled")
	        $('#show-result option[value=0]').attr("disabled", "disabled");
	        $('#show-result option[value='+place_horse_number+']').attr("disabled", "disabled");
	        if (win_horse_number != null) {
	            $('#show-result option[value='+win_horse_number+']').attr("disabled", "disabled");
	        }
	        if (show_horse_number != null) {
	            $('#win-result option[value='+show_horse_number+']').attr("disabled", "disabled");
	        }
	    }
	    if (finish == "show") {
	        win_horse_number = $('#win-result').val();
	        place_horse_number = $('#place-result').val();
	        show_horse_number = $('#show-result').val();
	        $('#win-result option').removeAttr("disabled")
	        $('#win-result option[value=0]').attr("disabled", "disabled");
	        $('#win-result option[value='+show_horse_number+']').attr("disabled", "disabled");
	        $('#place-result option').removeAttr("disabled")
	        $('#place-result option[value=0]').attr("disabled", "disabled");
	        $('#place-result option[value='+show_horse_number+']').attr("disabled", "disabled");
	        if (win_horse_number != null) {
	            $('#place-result option[value='+win_horse_number+']').attr("disabled", "disabled");
	        }
	        if (place_horse_number != null) {
	            $('#win-result option[value='+place_horse_number+']').attr("disabled", "disabled");
	        }
	    }
	}
	function enterResultFormHTML() {
	    $('.modal-footer button:last-of-type').attr('data-dismiss', 'modal');

		$('#message').html("<table class='scoreboard table table-borderless'>" + 
		"	<!-- Row A -->" +
		"	<thead>" +
		"		<tr id='title_row'>" +
		"			<td colspan='4'><img src='/images/kc-logo-white.svg' alt='<?php echo $_SESSION['site_name'];?> logo'><?php echo $_SESSION['site_name'];?></td>" +
		"		</tr>" +
		"		<tr>" +
		"			<th scope='col'>Horse#</th>" +
		"			<th scope='col'>Win</th>" +
		"			<th scope='col'>Place</th>" +
		"			<th scope='col'>Show</th>" +
		"		</tr>" +
		"	</thead>" +
		"	<!-- Row B -->" +
		"	<tr id='first'>" +
		"		<td>" +
		"			<select  id='win-result' class='race-result' onchange='set_horse_val(`win`)' required>" +
		"				<option value='0' selected disabled>Horse #</option>" +
		"			</select>" +
		"		</td>" +
		"		<td class='position-relative'>" +
		"			<input type='text' id='win1' class='w-100' required>" +
		"		</td>" +
		"		<td>" +
		"			<input type='text' id='place1' class='w-100' required>" +
		"		</td>" +
		"		<td>" +
		"			<input type='text' id='show1' class='w-100' required>" +
		"		</td>" +
		"	</tr>" +
		"	<!-- Row C -->" +
		"	<tr id='second'>" +
		"		<td>" +
		"			<select  id='place-result' class='race-result' onchange='set_horse_val(`place`)' required>" +
		"				<option value='0' selected disabled>Horse #</option>" +
		"			</select>" +
		"		</td>" +
		"		<td></td>" +
		"		<td class='position-relative'>" +
		"			<input type='text' id='place2' class='w-100' required>" +
		"		</td>" +
		"		<td class='position-relative'>" +
		"			<input type='text' id='show2' class='w-100' required>" +
		"		</td>" +
		"	</tr>" +
		"	<!-- Row D -->" +
		"	<tr id='third'>" +
		"		<td>" +
		"			<select id='show-result' class='race-result' onchange='set_horse_val(`show`)' required>" +
		"				<option value='0' selected disabled>Horse #</option>" +
		"			</select>" +
		"		</td>" +
		"		<td></td>" +
		"		<td></td>" +
		"		<td class='position-relative'>" +
		"			<input type='text' id='show3' class='w-100' required>" +
		"		</td>" +
		"	</tr>" +
		"</table>");
	}

	function depopulateHorses() {
	    $( ".race-result" ).each( function () {
	        $(".race-result option").remove();
	    });
	    $('#message table').remove();
	};


	function populateHorses(raceNumber, eventNumber) {
	    enterResultFormHTML();
	    for (let i = 0; i < horseList.length; i++) {
	        $('#win-result').append('<option value=' + horseList[i] + '>' + horseList[i] + '</option>');
	        $('#place-result').append('<option value=' + horseList[i] + '>' + horseList[i] + '</option>');
	        $('#show-result').append('<option value=' + horseList[i] + '>' + horseList[i] + '</option>');
	    }
	};

	function enterResultForRace(eventNumber, raceNumber) {
	    $('#collapse' + raceNumber).addClass('show');
	    let oldWin = null;
	    let oldPlace = null;
	    let oldShow = null;
	    let win = [];
	    win.push($('#win-result').val());
	    win.push($('#win1').val());
	    win.push($('#place1').val());
	    win.push($('#show1').val());
	    let place = [];
	    place.push($('#place-result').val());
	    place.push($('#place2').val());
	    place.push($('#show2').val());
	    let show = [];
	    show.push($('#show-result').val());
	    show.push($('#show3').val());
	    depopulateHorses();
	    let data = {win: win, place: place, show: show}
	    if (oldWin != null && oldPlace != null && oldShow != null)
	        data = {win: win, place: place, show: show, old_win: oldWin, old_place: oldPlace, old_show: oldShow}
	    $.ajax({
	        method: 'POST',
	        url: '/admin/events/race.php?e=' + eventNumber + '&r=' + raceNumber + '&q=' + 5,
	        data: data,
	        dataType: 'json',
	        success: function (data) {
	            $('main').prepend(data['alert']);
	            $('#alert').delay( 3000 ).fadeOut( 400 );
	        }
	    });
	    location.reload();
	}
	function cancelRace(eventNumber, raceNumber) {
	    console.log('Cancelling event ' + eventNumber + ' race ' + raceNumber);
	    document.getElementById('cancel_race_form').submit();
	    console.log('done');
	}
</script>
{main_nav}
<main role="main" id="races_page" style="background-image: url(<?php echo $background_image['filename'];?>);">
	<div class="card" id="pick_block" style="">
        <div class="input-group input-group-lg mb-2 pt-2">
            <div class="input-group-prepend">
                <label class="input-group-text" for="race_picker">Race</label>
			</div>
            <select class="custom-select" id="race_picker">
                <?php
                    for($i = 1; $i <= $num_races; $i++) {
                        if ($i == $race) {
                            if ($memorial_race_number == $i) {
                                echo "<option value='e=$event&r=$i&u=$uid' selected disabled>{$_SESSION['site_memorial_race_name']}</option>";
                            }
                            else {
                                echo "<option value='e=$event&r=$i&u=$uid' selected disabled>Race $i</option>";
                            }
                        }
                        else {
                            if ($memorial_race_number == $i) {
                                echo "<option value='e=$event&r=$i&u=$uid'>{$_SESSION['site_memorial_race_name']}</option>";
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
        <?php 
        if ($race_info) { // Checks to see if there is race info for the race (used to check 'All Races')
            if ($race_info['cancelled'] == '1') {
                echo <<< CANCEL
                <div class="card-text">
                    <h1>Race has been canceled!</h1>
                    <h1>No bets will be processed for this race.</h1>
                </div>
CANCEL;
            }
            else {
                if($race_info['window_closed'] == '0' && $race_info['cancelled'] == 0) {?>
                <form action="bets.php" method="POST">
                    <div class="card-body form-row ">
                        <div class="col-xl-6 form-group input-group input-group-lg mb-2">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="horseSelection">Pick</label>
                            </div>
                            <select class="custom-select" id="horseSelection" name="horseSelection" required>
                                <?php
                                    $options = '';
									foreach($horses as $key => $value){
										if (($pick && ($value['horse_number'] == $pick['horse_number'])) || (($old_pick != -1) && ($value['horse_number'] == $old_pick))) {
											$pick_selected = 'selected';
											$no_pick_selected = '';
										} else {
											$pick_selected = '';
											$no_pick_selected = 'selected';
										}
										$options .= "<option $pick_selected>{$value['horse_number']}</option>";
									}
									echo "<option value='default' disabled $no_pick_selected>Horse...</option>";
									echo $options;
                                ?>
                            </select>
                        </div>
                        <div class="col-xl-6 form-group input-group input-group-lg 2">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="placeSelection">Finish</label>
                            </div>
                            <select class="custom-select" id="placeSelection" name="placeSelection" required>
                                <!-- If no finish has been selected, default to 'Choose' -->
                                <option value="default" selected disabled>Choose...</option>
                                <?php 
                                if (($pick && ($pick['finish'] == 'win')) || (($old_finish != -1) && ($old_finish == 'win'))) {
                                    echo "<option value='win' selected>Win</option>";
                                }
                                else {
                                    echo "<option value='win'>Win</option>";
                                }
                                if (($pick && ($pick['finish'] == 'place')) || (($old_finish != -1) && ($old_finish == 'place'))) {
                                    echo "<option value='place' selected>Place</option>";
                                }
                                else {
                                    echo "<option value='place'>Place</option>";
                                }
                                if (($pick && ($pick['finish'] == 'show')) || (($old_finish != -1) && ($old_finish == 'show'))) {
                                    echo "<option value='show' selected>Show</option>";
                                }
                                else {
                                    echo "<option value='show'>Show</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" value="<?php echo $race; ?>" name="currentRace" id="currentRace">
                        <div>
                            <input class="btn btn-primary" type="submit" value="Submit">
                            <?php
                                if ($_SESSION['admin']) {
                                    echo <<< ADMINPORTAL
                            <a href="/admin/events/manage.php?e=$event" class="btn btn-secondary">Admin Manage Race Portal</a>
ADMINPORTAL;

                                }
                            ?>
                        </div>
                    </div>
                </form>

        <?php } 
        if ($race_info['window_closed'] == '1') {
			?>
            <div class="card-body" id="window_closed">
				<div class="card-text" id="no_pick">
                <?php 
                    if($pick){ ?>
                        <h1>You picked <?php echo "<span class='horse-picks animate__animated animate__delay-1s animate__zoomIn'>{$pick['horse_number']}</span>&nbsp;to&nbsp;<span class='horse-picks animate__animated animate__delay-1s animate__zoomIn'>" . ucfirst($pick['finish']) . "</span>";?></h1>
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
                            <table class="scoreboard animate__animated animate__zoomIn">
                                <tr id="title_row">
                                    <td colspan="5"><img src="/images/kc-logo-white.svg" alt="{$_SESSION['site_name']} logo"> {$_SESSION['site_name']}</td>
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
        }
        else { // Comes here when showing 'All Races'
            echo "<div class='card-body' id='window_closed'><h1 class='card-title'>Current Event Leaderboard</h1></div>";
        }?>
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
    </div><!-- END #pick_block -->
    
</main>
{footer}
<?php ob_end_flush(); ?>