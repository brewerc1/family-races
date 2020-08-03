<?php

// Refactoring in Progress

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
//ob_start('template');
session_start();

// set the page title for the template
//$page_title = "Manage an Event";
//$javascript = "";

if (!isset($_SESSION["id"])) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;

} elseif ($_SESSION["id"] == 0) {
    header("Location: /login/");
    // Make sure the rest of code is not gonna be executed
    exit;
}

// To be reviewed
if (!$_SESSION["admin"]) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}

if (!isset($_GET["r"]) && !isset($_GET["q"]) && !isset($_GET["e"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}


// All of these arrays are used to isolate each action handled by the current file.
$request_array = array(
    1 => "window_closed",
    2 => "cancelled"
);

$update = array(
    3 => "update"
);

$delete = array(
    4 => "Delete"
);

$result = array(
    5 => "post"
);

$get_result = array(
    6 => "get"
);


/**
 * @param $int
 * @return mixed
 *
 * Used to validate the int received from $_GET
 */
function validateInt($int) {
    $int_options = array("options" =>
        array("min_range" =>1 ));

    $in = filter_var(trim($int), FILTER_SANITIZE_NUMBER_INT);
    return filter_var($in, FILTER_VALIDATE_INT, $int_options);
}


/**
 * @param $message
 * @param string $alert_style
 * @return string
 *
 * Used to send alert to the requester
 */
function alert($message, $alert_style ='success') {
    return <<< HTML
        <div class="floating-alert alert alert-$alert_style alert-dismissible fade show fixed-top mt-5 mx-4" role="alert" id="alert">
          $message
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
HTML;
}

$q = validateInt($_GET["q"]);
$race_number = validateInt($_GET["r"]);
$event_id = validateInt($_GET["e"]);

// Update window_closed/cancelled
if (key_exists($q, $request_array)) {


    /**
     * @param $pdo
     * @param $field_name
     * @param $race_number
     * @param $value
     * @param $message1
     * @param $message2
     * @param $message3
     *
     * Encapsulates logic on cancelling or opening or closing a Race
     */
    function raceUpdate($pdo, $field_name, $race_number, $value, $message1, $message2, $message3) {
        $update_query = "UPDATE race SET " . $field_name . " = :respond WHERE race_number = :race_number";
        $stmt = $pdo->prepare($update_query);
        if ($stmt->execute(["respond" => $value, 'race_number' => $race_number])) {

            $query = "SELECT ". $field_name ." FROM race WHERE race_number = :race_number";
            $race = $pdo->prepare($query);
            if ($race->execute(['race_number' => $race_number]) && $race->rowCount() > 0) {
                $respond = $race->fetch()[$field_name];
                $message = $respond ? $message1 : $message2;
                echo alert($message);

            } else echo alert("Something went wrong, please refresh the page.", 'warning');


        } else echo alert($message3,"warning");
    }


    // Cancel A Race
    if (isset($_POST["is_checked"])) {
        raceUpdate($pdo, $request_array[$q], $race_number,
            $_POST["is_checked"], "Race $race_number is cancelled",
            "Race $race_number is uncancelled",
            "Can't cancel Race $race_number at the moment, please try again");
    }

    // Close/Reopen betting window
    if (isset($_POST["open"])) {
        raceUpdate($pdo, $request_array[$q], $race_number,
            intval($_POST["open"]), "Race $race_number window is closed",
            "Race $race_number window is reopened",
            "Can't reopen Race $race_number window at the moment, please try again");
    }
}

// Update horses for a race
if (key_exists($q, $update)) {
    if (isset($_POST["horse_array"])) {

        // Delete all horses associated to the (race number and event number)
        $query = "DELETE FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
        $horses = $pdo->prepare($query);
        if ($horses->execute(["race_event_id" => $event_id, "race_race_number" => $race_number])) {
            // Insert the New horses only if the old ones were removed from DB
            $query = "INSERT INTO horse ( race_event_id, race_race_number, horse_number ) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($query);

            $horses = $_POST["horse_array"];
            foreach ($horses as $horse) {
                if (!empty($horse)) {
                    // Subject to changed or be removed (line 147)
                    $horse = filter_var($horse, FILTER_SANITIZE_STRING);

                    $success = $stmt->execute([$event_id, $race_number, $horse]);
                }
            }
            if ($success)
                echo alert("Race $race_number is updated.");

        }

    }
}

// Delete A race
if (key_exists($q, $delete)) {
    // Delete all horses associated to the (race number to be deleted and event number)
    $query = "DELETE FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
    $horses = $pdo->prepare($query);
    if ($horses->execute(["race_event_id" => $event_id, "race_race_number" => $race_number])) {

        // Delete the row in the race table where race number matched the race number to be deleted
        $query = "DELETE FROM race WHERE event_id = :event_id AND race_number = :race_number";
        $race = $pdo->prepare($query);
        if ($race->execute(['event_id' => $event_id,'race_number' => $race_number])) {
            echo alert("Race $race_number is deleted.");

        } else echo alert("Something went wrong, Complete the deletion for Race $race_number",
            "warning");

    } else echo alert("Server ERROR, try again.", "warning");

//    echo alert("Race $race_number is deleted.");
}


// Save or Update Race result
if (key_exists($q, $result)) {

    /**
     * @param $pdo
     * @param $event_id
     * @param $race_number
     * @param $win_horse
     * @param $win_purse
     * @param $place_purse
     * @param $show_purse
     * @param $place_horse
     * @param $place_purse2
     * @param $show_purse2
     * @param $show_horse
     * @param $show_purse3
     * @param string $win
     * @param string $place
     * @param string $show
     *
     * Writes results to DB
     */
    function saveResult($pdo, $event_id, $race_number, $win_horse, $win_purse,
                        $place_purse, $show_purse, $place_horse, $place_purse2,
                        $show_purse2, $show_horse, $show_purse3, $win='win',
                        $place='place', $show='show' ) {

        $query = "UPDATE horse SET finish = :finish, win_purse = :win_purse, place_purse = :place_purse, show_purse = :show_purse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
        $stmt = $pdo->prepare($query);

        $stmt->execute(['finish' => $win, 'win_purse' => $win_purse, 'place_purse' => $place_purse, 'show_purse' => $show_purse,
            'race_event_id' => $event_id, 'race_race_number' => $race_number,
            'horse_number' => $win_horse]);

        $stmt->execute(['finish' => $place, 'win_purse' => NULL, 'place_purse' => $place_purse2, 'show_purse' => $show_purse2,
            'race_event_id' => $event_id, 'race_race_number' => $race_number,
            'horse_number' => $place_horse]);

        $stmt->execute(['finish' => $show, 'win_purse' => NULL, 'place_purse' => NULL, 'show_purse' => $show_purse3,
            'race_event_id' => $event_id, 'race_race_number' => $race_number,
            'horse_number' => $show_horse]);

    }

    /**
     * @return bool
     *  Check if all $_POST variables are not empty or null
     */
    function emptyResult() {
        return (
            !empty($_POST["win"][0]) && !empty($_POST["win"][1]) && !empty($_POST["win"][2]) &&
            !empty($_POST["win"][3]) && !empty($_POST["place"][0]) && !empty($_POST["place"][1]) &&
            !empty($_POST["place"][2]) && !empty($_POST["show"][0]) && !empty($_POST["show"][1])
        );
    }

    if (isset($_POST["old_win"])) {

        // Update result
        if (emptyResult()) {

            // Set the old one to Null
            saveResult($pdo, $event_id, $race_number, $_POST["old_win"][0], NULL,
                NULL, NULL, $_POST["old_place"][0], NULL,
                NULL, $_POST["old_show"][0], NULL, NULL, NULL, NULL);

            // Write the new result to DB
            saveResult($pdo, $event_id, $race_number, $_POST["win"][0], $_POST["win"][1],
                $_POST["win"][2], $_POST["win"][3], $_POST["place"][0], $_POST["place"][1],
                $_POST["place"][2], $_POST["show"][0], $_POST["show"][1]);

            echo json_encode(array('saved' => 1, 'alert' =>
                alert("Race $race_number Results are modified")));
        } else {
            echo json_encode(array('saved' => 0, 'alert' =>
                alert("Race $race_number Results cannot have empty fields, old results are kept", "warning")));
        }

    } else {
        // Save First time result
        if (emptyResult()) {

            // Write result to DB
            saveResult($pdo, $event_id, $race_number, $_POST["win"][0], $_POST["win"][1],
                $_POST["win"][2], $_POST["win"][3], $_POST["place"][0], $_POST["place"][1],
                $_POST["place"][2], $_POST["show"][0], $_POST["show"][1]);

            echo json_encode(array('saved' => 1, 'alert' =>
                alert("Race $race_number Results are saved")));
        } else {
            echo json_encode(array('saved' => 0, 'alert' =>
                alert("Race $race_number Results cannot have empty fields", "warning")));
        }

    }
}

// Get the result for a race and display it in the edit race result modal
if (key_exists($q, $get_result)) {
    $data = array();

    $query = "SELECT horse_number, win_purse, place_purse, show_purse FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND finish = :finish";
    $stmt = $pdo->prepare($query);

    $success = $stmt->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number, 'finish' => 'win']);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $data['win'] = $row;
    }

    $query = "SELECT horse_number, place_purse, show_purse FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND finish = :finish";
    $stmt = $pdo->prepare($query);

    $success = $stmt->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number, 'finish' => 'place']);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $data['place'] = $row;
    }

    $query = "SELECT horse_number, show_purse FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND finish = :finish";
    $stmt = $pdo->prepare($query);

    $success = $stmt->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number, 'finish' => 'show']);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $data['show'] = $row;
    }

    if ($success)
        echo json_encode($data);

}
