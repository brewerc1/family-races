<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');


// Testing only
$_SESSION['id'] = '1';
$_SESSION['admin'] = 1;

use api\Utils;
include_once '../Utils.php';

if(!Utils::isLoggedIn()) {
    Utils::sendResponse(401, $success=false, $msg=["Authentication is required."]);
    exit;
}

function validGetRequestURLParams() {
    if (count($_GET) > 2) return false;
    if (key_exists("e", $_GET) && Utils::getEventId() === null) return false;
    if (key_exists("r", $_GET)) {
        if (Utils::getRaceNumber() === null) return false;
        if (!key_exists("e", $_GET)) return false;
        elseif (Utils::getEventId() === null) return false;
    }
    return true;
}

// GET ALL races (ALL, ALL for event, a race)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && validGetRequestURLParams()) {
    try {
        $query = "SELECT * FROM race LIMIT :_limit OFFSET :off_set";
        $OptionsForQuery = [];
        $pageQuery = "SELECT COUNT(*) AS total FROM race";
        $optionForPageQuery = [];
        $urlParams = [];

        if (key_exists("e", $_GET) && Utils::getEventId() !== null) {
            // Check if there is an event with the id in url params['e']
            $query = "SELECT * FROM event WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(["id" => Utils::getEventId()]);
            if ($stmt->rowCount() !== 1) {
                Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
                exit;
            }

            $urlParams["e"] = Utils::getEventId();

            if (Utils::getRaceNumber() === null) {
                // Get all races for event
                $query = "SELECT * FROM race WHERE event_id = :event_id LIMIT :_limit OFFSET :off_set";
                $OptionsForQuery = ["event_id" => Utils::getEventId()];
                $pageQuery = "SELECT COUNT(*) AS total FROM race WHERE event_id = :event_id";
                $optionForPageQuery = ["event_id" => Utils::getEventId()];
            }
            else {
                $urlParams["r"] = Utils::getRaceNumber();

                $query = "SELECT * FROM race WHERE event_id = :event_id AND race_number = :race_number LIMIT :_limit OFFSET :off_set";
                $OptionsForQuery = ["event_id" => Utils::getEventId(), "race_number" => Utils::getRaceNumber()];
                $pageQuery = "SELECT COUNT(*) AS total FROM race WHERE event_id = :event_id AND race_number = :race_number";
                $optionForPageQuery = ["event_id" => Utils::getEventId(), "race_number" => Utils::getRaceNumber()];
            }
        }

        $endPoint = "/api/races/";
        $keyword = "races";
        $raceData = Utils::getAllWithPagination($pdo, $endPoint, $keyword,
            $query, $pageQuery, $OptionsForQuery, $optionForPageQuery, $urlParams);

        if (key_exists("pageNotFound", $raceData)) {
            Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
            exit;
        }

        $query = "SELECT * FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
        $stmt = $pdo->prepare($query);

        $horseQuery = "SELECT * FROM pick WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
        $horseStmt = $pdo->prepare($horseQuery);

        // Get horses for each race
        $races = array();
        foreach ($raceData["races"] as $race) {
            $stmt->execute(["race_event_id" => $race["event_id"], "race_race_number" => $race["race_number"]]);
            $horses = $stmt->fetchAll();
            $horsesVal = array();

            // Answer: Whether horse can be deleted
            foreach ($horses as $horse) {
                $horseStmt->execute(["race_event_id" => $horse["race_event_id"],
                    "race_race_number" => $horse["race_race_number"], "horse_number" => $horse["horse_number"]]);

                if ($horseStmt->rowCount() > 0) {
                    $horse["can_be_delete"] = false;
                } else {
                    $horse["can_be_delete"] = true;
                }
                $horsesVal[] = $horse;
            }

            $race["horses"] = $horsesVal;
            $races[] = $race;
        }

        $raceData["races"] = $races;
        Utils::sendResponse(200, $success=true, $msg=null, $data=$raceData);
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }
}

// TODO: Not done
// CREATE race
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET)) {
    // Admin only View
    if (!Utils::isAdmin()) {
        Utils::sendResponse(403, $success=false, $msg=["Forbidden"], $data=null);
        exit;
    }

    if (!Utils::isValidContentType()) {
        Utils::sendResponse(400, $success=false, $msg=["Content type must be set to application/json"], $data=null);
        exit;
    }

    $postData = file_get_contents('php://input');
    if (!$jsonData = json_decode($postData)) {
        Utils::sendResponse(400, $success=false, $msg=["Request body is not valid JSON."], $data=null);
        exit;
    }

    // Required data && Optional data
    if (empty($jsonData->event_id) || isset($jsonData->window_closed) || isset($jsonData->cancelled) || isset($jsonData->horses)) {
        $messages = array();
        // Required
        (empty($jsonData->event_id) || !is_numeric($jsonData->event_id) ?
            $messages[] = "event_id field is required and must be numeric" : false);

        // Optionals
        (isset($jsonData->window_closed) && !(is_numeric($jsonData->window_closed) &&
            (intval($jsonData->window_closed) === 0 || intval($jsonData->window_closed === 1))) ?
            $messages[] = "window_closed field is must be numeric 0(open) or 1(close)" : false);

        (isset($jsonData->cancelled) && !(is_numeric($jsonData->cancelled) &&
            (intval($jsonData->cancelled) === 0 || intval($jsonData->cancelled) === 1)) ?
            $messages[] = "cancelled field is must be numeric 0(open) or 1(close)" : false);

        ((isset($jsonData->horses) && !is_array($jsonData->horses)) ?
            $messages[] = "horses field must be an array of string" : false);

        if (count($messages) > 0) {
            Utils::sendResponse(400, $success = false, $msg = $messages, $data = null);
            exit;
        }
    }
    //Required
    $eventId = $jsonData->event_id;
    //Optionals
    $windowClosed = isset($jsonData->window_closed) ? $jsonData->window_closed : null;
    $cancelled = isset($jsonData->cancelled) ? $jsonData->cancelled : null;
    $horses = isset($jsonData->horses) ? $jsonData->horses : null;

    try {
        // Create race 2 steps

        // Step 1: Insert into race table
        $val = "(event_id,";
        $aliases = "(:event_id,";
        $option = ["event_id" => $eventId];

        if ($windowClosed !== null) {
            $val .= "window_closed,";
            $aliases .= ":window_closed,";
            $option = ["window_closed" => $windowClosed];
        }

        if ($cancelled !== null) {
            $val .= "cancelled,";
            $aliases .= ":cancelled,";
            $option = ["cancelled" => $cancelled];
        }

        $val = substr($val, 0, -1) . ")";
        $aliases = substr($aliases, 0, -1) . ")";

        // TODO: race_number is not defaulted in db

        $pdo->beginTransaction();
        $query = "INSERT INTO race " . $val . " VALUES " . $aliases;
        $stmt = $pdo->prepare($query);
        $stmt->execute($option);
        $createdRace = $pdo->lastInsertId();
        $pdo->commit();

        $query = "SELECT * FROM race WHERE event_id = :event_id AND race_number = :race_number";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['event_id' => $eventId, 'race_number' => $createdRace]);

        Utils::sendResponse(201, $success=true, $msg=["Race created"], $data=$stmt->fetchAll());
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: " . $ex], $data=null);
        exit;
    }

}

// UPDATE/DELETE race
elseif (($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH' ||
        $_SERVER['REQUEST_METHOD'] === 'DELETE') && (Utils::getEventId() !== null && Utils::getRaceNumber() !== null)) {

    // Admin only View
    if (!Utils::isAdmin()) {
        Utils::sendResponse(403, $success=false, $msg=["Forbidden"], $data=null);
        exit;
    }

    // DELETE race
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        try {
            $pdo->beginTransaction();
            $query = "DELETE FROM race WHERE event_id = :event_id AND race_number = :race_number";
            $stmt = $pdo->prepare($query);
            $stmt->execute(["event_id" => Utils::getEventId(), "race_number" => Utils::getRaceNumber()]);
            $pdo->commit();

            Utils::sendResponse(204, $success=true, $msg=[], $data=null);
            exit;

        }
        catch (PDOException $ex) {
            Utils::sendResponse(500, $success=false, $msg=["This race may contain horses"], $data=null);
            exit;
        }
    }
    // UPDATE Race
    else {

        if (!Utils::isValidContentType()) {
            Utils::sendResponse(400, $success=false, $msg=["Content type must be set to application/json"], $data=null);
            exit;
        }

        $postData = file_get_contents('php://input');
        if (!$jsonData = json_decode($postData)) {
            Utils::sendResponse(400, $success=false, $msg=["Request body is not valid JSON."], $data=null);
            exit;
        }

        if (isset($jsonData->window_closed) || isset($jsonData->cancelled)) {
            $messages = [];
            // Optionals
            (isset($jsonData->window_closed) && !(is_numeric($jsonData->window_closed) &&
                (intval($jsonData->window_closed) === 0 || intval($jsonData->window_closed === 1))) ?
                $messages[] = "window_closed field is must be numeric 0(open) or 1(close)" : false);

            (isset($jsonData->cancelled) && !(is_numeric($jsonData->cancelled) &&
                (intval($jsonData->cancelled) === 0 || intval($jsonData->cancelled) === 1)) ?
                $messages[] = "cancelled field is must be numeric 0(open) or 1(close)" : false);

            if (count($messages) > 0) {
                Utils::sendResponse(400, $success = false, $msg = $messages, $data = null);
                exit;
            }
        }

        //Optionals
        $windowClosed = isset($jsonData->window_closed) ? $jsonData->window_closed : null;
        $cancelled = isset($jsonData->cancelled) ? $jsonData->cancelled : null;

        // Update
        try {
            $update = "";
            $option = ["event_id" => Utils::getEventId(), "race_number" => Utils::getRaceNumber()];

            if ($windowClosed !== null) {
                $update .= "window_closed = :window_closed,";
                $option["window_closed"] = $windowClosed;
            }

            if ($cancelled !== null) {
                $update .= "cancelled = :cancelled";
                $option["cancelled"] = $cancelled;
            }

            $update = (substr($update, -1) === ',') ? substr($update, 0, -1) : $update;

            $pdo->beginTransaction();
            $query = "UPDATE race SET " . $update . " WHERE event_id = :event_id AND race_number = :race_number";
            $stmt = $pdo->prepare($query);
            $stmt->execute($option);
            $pdo->commit();

            $query = "SELECT * FROM race WHERE event_id = :event_id AND race_number = :race_number";
            $stmt = $pdo->prepare($query);
            $stmt->execute(["event_id" => Utils::getEventId(), "race_number" => Utils::getRaceNumber()]);

            Utils::sendResponse(200, $success=true, $msg=["Race updated"], $data=$stmt->fetchAll());
            exit;
        }
        catch (PDOException $ex) {
            Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
            exit;
        }
    }
}

// ANY UNSUPPORTED OPERATION
else {
    Utils::sendResponse(405, $success=false, $msg=["Method not allowed."], $data=null);
    exit;
}

