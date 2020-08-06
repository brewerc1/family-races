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

//!isset($_GET["r"]) &&
if (!isset($_GET["r"]) && !isset($_GET["q"]) && !isset($_GET["e"])) {
    header("HTTP/1.1 401 Unauthorized");
    // An error page
    //header("Location: error401.php");
    exit;
}


$site_default_horse_count = !empty($_SESSION["site_default_horse_count"]) ?
    $_SESSION["site_default_horse_count"] : 1;

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

$addRace = array(
    7 => "Add a Race"
);

$edit_pot = array(
    8 => "pot"
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
     * @param $event_id
     * @param $value
     * @param $message1
     * @param $message2
     * @param $message3
     *
     * Encapsulates logic on cancelling or opening or closing a Race
     */
    function raceUpdate($pdo, $field_name, $race_number, $event_id, $value, $message1, $message2, $message3) {
        $update_query = "UPDATE race SET " . $field_name . " = :respond WHERE event_id = :event_id AND race_number = :race_number";
        $stmt = $pdo->prepare($update_query);
        if ($stmt->execute(["respond" => $value, 'event_id' => $event_id, 'race_number' => $race_number])) {

            $query = "SELECT ". $field_name ." FROM race WHERE event_id = :event_id AND race_number = :race_number";
            $race = $pdo->prepare($query);
            if ($race->execute(['event_id' => $event_id, 'race_number' => $race_number]) && $race->rowCount() > 0) {
                $respond = $race->fetch()[$field_name];
                $message = $respond ? $message1 : $message2;
                echo alert($message);

            } else echo alert("Something went wrong, please refresh the page.", 'warning');


        } else echo alert($message3,"warning");
    }




    // Cancel A Race
    if (isset($_POST["is_checked"])) {
        raceUpdate($pdo, $request_array[$q], $race_number, $event_id,
            $_POST["is_checked"], "Race $race_number is cancelled",
            "Race $race_number is uncancelled",
            "Can't cancel Race $race_number at the moment, please try again");
    }




    // Close/Reopen betting window
    if (isset($_POST["open"])) {
        raceUpdate($pdo, $request_array[$q], $race_number, $event_id,
            intval($_POST["open"]), "Race $race_number window is closed",
            "Race $race_number window is reopened",
            "Can't reopen Race $race_number window at the moment, please try again");
    }




}









/**
 *  Actions
 *
 *  Insert horses
 *  Delete horses
 *  Create race if not exist
 */
if (key_exists($q, $update)) {



    /**
     * @param $pdo
     * @param $event_id
     * @param $race_number
     * @return mixed
     *
     * Returns list of horses to be displayed in the UI
     */
    function getHorses($pdo, $event_id, $race_number) {
        $horses_array = array();
        $query = "SELECT horse_number FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
        $horses = $pdo->prepare($query);
        $horses->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number]);
        $horses = $horses->fetchAll();

        foreach ($horses as $row)
            array_push($horses_array, $row['horse_number']);

        return $horses_array;
    }



    /**
     * @param $pdo
     * @param $event_id
     * @param $race_number
     * @param $horse
     * @return bool
     *
     * Checks if a horse number already exists (avoid duplicate of horse number for same (event and race)
     */
    function exist($pdo, $event_id, $race_number, $horse) {
        $query = "SELECT * FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
        $h = $pdo->prepare($query);
        $h->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number, 'horse_number' => $horse]);
        return $h->rowCount() > 0;
    }





    // Check if the race exists
    $race_query = "SELECT * FROM race WHERE event_id = :event_id AND race_number = :race_number";
    $race_stmt = $pdo->prepare($race_query);
    $race_stmt->execute(['event_id' => $event_id, 'race_number' => $race_number]);
    if (!$race_stmt->rowCount() > 0) {

        // Create the race
        $horses = array();
        $race_query = "INSERT INTO race (event_id, race_number) VALUES (:event_id, :race_number)";
        $race_stmt = $pdo->prepare($race_query);
        if (!$race_stmt->execute(['event_id' => $event_id,
            'race_number' => $race_number])) {

            if (!empty($_POST["horse_array"])) {
                foreach ($_POST["horse_array"] as $horses) {
                    $horse = filter_var($horse, FILTER_SANITIZE_STRING);
                    array_push($horses, $horse);
                }
            }
            echo json_encode(array('added' => 1,
                'alert' => alert("Race $race_number added"),
                'horses' => $horses));
        } else {
            if (empty($horses))
                echo json_encode(array('added' => 1,
                    'alert' => alert("Race $race_number added"),
                    'horses' => ['']));
        }
    }









    /**
     * Delete horses in DB
     *
     * First: Check if there is no bet entries for the targeted horse
     * Second: Delete horse only if there is no bet entries otherwise do not delete
     *
     */

    if (!empty($_POST["delete_horse"])) {
        $horses_in_pick_table = array();

        $success = 0;
        $pick_query = "SELECT * FROM pick WHERE horse_number = :horse_number";
        $pick = $pdo->prepare($pick_query);

        $query = "DELETE FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
        $stmt = $pdo->prepare($query);

        foreach ($_POST["delete_horse"] as $horse) {
            if (!empty($horse)) {
                $pick->execute(['horse_number' => $horse]);
                if ($pick->rowCount() > 0) {
                    $success = 1;
                    $horse = filter_var($horse, FILTER_SANITIZE_STRING);
                    array_push($horses_in_pick_table, $horse);

                } else {
                    $success = 1;
                    $stmt->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number, 'horse_number' => $horse]);
                }
            }
        }

        $message = "Race $race_number is updated.";
        if (count($horses_in_pick_table) > 0) {
            $message .= " Can't delete";
            foreach ($horses_in_pick_table as $horse)
                $message .= " " . $horse;
        }

        if ($success)
            echo json_encode(array('added' => 1,
                'alert' => alert($message),
                'horses' => getHorses($pdo, $event_id, $race_number)));
        else echo json_encode(array('added' => 1,
            'alert' => alert("Something went wrong, please try again", "warning"),
            'horses' => getHorses($pdo, $event_id, $race_number)));

    }







    /**
     * Insert horses in DB
     *
     * Only insert horses that aren't in DB for the race number and event id
     *
     *  Number of horses in DB must be less or equal to the default horse count
     */
    if (!empty($_POST["horse_array"])) {

        $success = 0;
        $horses_array = array();

        // Get total number of horses in DB
        $current_horse_count_in_DB = 0;
        $query = "SELECT * FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number]);
        $horses = $stmt->fetchAll();
        $current_horse_count_in_DB = $stmt->rowCount();
        $number_of_horses_to_be_inserted = $site_default_horse_count - $current_horse_count_in_DB;

        // Insert Horses to DB
        $query = "INSERT INTO horse (race_event_id, race_race_number, horse_number) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);

        foreach ($_POST["horse_array"] as $horse) {

            if (!exist($pdo, $event_id, $race_number, $horse) && $number_of_horses_to_be_inserted > 0) {
                $success = $stmt->execute([$event_id, $race_number, $horse]);
            }
            $number_of_horses_to_be_inserted--;
        }

        foreach ($horses as $row)
            array_push($horses_array, $row['horse_number']);

        if ($success)
            echo json_encode(array('added' => 1,
                'alert' => alert("Race $race_number is updated"),
                'horses' => $horses_array));
        else echo json_encode(array('added' => 1,
            'alert' => alert("Something went wrong. Please, try again", "warning"),
            'horses' => $horses_array));
    }

}







/**
 *  Actions
 *
 *  Delete race
 */
if (key_exists($q, $delete)) {


    $query = "DELETE FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
    $horses = $pdo->prepare($query);
    if ($horses->execute(["race_event_id" => $event_id, "race_race_number" => $race_number])) {


        $query = "DELETE FROM race WHERE event_id = :event_id AND race_number = :race_number";
        $race = $pdo->prepare($query);
        if ($race->execute(['event_id' => $event_id,'race_number' => $race_number])) {
            echo json_encode(array("deleted" => 1,
                "alert" => alert("Race $race_number is deleted.")));

        } else echo json_encode(array("deleted" => 0,
            'alert' => alert("Something went wrong, Complete the deletion for Race $race_number",
            "warning")));

    } else echo json_encode(array("deleted" => 0,
        "alert" => alert("Server ERROR, try again.", "warning")));

}






/**
 *  Actions
 *
 *  Save race Result
 *  Update race Result
 */
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
    function notEmptyResult() {
        return (
            !empty($_POST["win"][0]) && !empty($_POST["win"][1]) && !empty($_POST["win"][2]) &&
            !empty($_POST["win"][3]) && !empty($_POST["place"][0]) && !empty($_POST["place"][1]) &&
            !empty($_POST["place"][2]) && !empty($_POST["show"][0]) && !empty($_POST["show"][1])
        );
    }


    if (isset($_POST["old_win"])) {

        // Update result
        if (notEmptyResult()) {

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
        if (notEmptyResult()) {

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






/**
 *  Actions
 *
 *  Get race results
 */
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








/**
 *  Actions
 *
 *  Edit jackpot
 */
if (key_exists($q, $edit_pot)) {

    if (!empty($_POST['pot'])) {

        $query = "UPDATE event SET pot = :pot WHERE id = :id";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute(['pot' => $_POST['pot'], 'id' => $event_id])) {

            $query = "SELECT pot FROM event WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $event_id]);

            $pot = $stmt->fetch()['pot'];
            echo json_encode(array('edited' => 1,
                'alert' => alert("Jackpot updated"), 'pot' => $pot));
        }
        else echo json_encode(array('edited' => 0,
            'alert' => alert("Something went wrong", "warning")));

    }
}
