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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // All results per event
    // All results per race
    //

    if ((!isset($_GET['e']) || !is_numeric($_GET['e'])) || (!isset($_GET['r']) || !is_numeric($_GET['r']))) {
        Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
        exit;
    }

    try {
        $query = "SELECT horse_number, win_purse, place_purse, show_purse FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND win_purse IS NOT NULL AND place_purse IS NOT NULL AND show_purse IS NOT NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['race_event_id' => Utils::getEventId(), 'race_race_number' => Utils::getRaceNumber()]);
        $win = $stmt->fetch();
        $winHorse = Utils::getHorses($pdo, Utils::getEventId(), Utils::getRaceNumber(), null, $win["horse_number"])[0];
//        var_dump($winHorse);
//        var_dump($win);

        $query = "SELECT horse_number, place_purse, show_purse FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND win_purse IS NULL AND place_purse IS NOT NULL AND show_purse IS NOT NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['race_event_id' => Utils::getEventId(), 'race_race_number' => Utils::getRaceNumber()]);
        $place = $stmt->fetch();
        $placeHorse = Utils::getHorses($pdo, Utils::getEventId(), Utils::getRaceNumber(), null, $place["horse_number"])[0];
//        var_dump($placeHorse);
//        var_dump($place);
//
        $query = "SELECT horse_number, show_purse FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND win_purse IS NULL AND place_purse IS NULL AND show_purse IS NOT NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['race_event_id' => Utils::getEventId(), 'race_race_number' => Utils::getRaceNumber()]);
        $show = $stmt->fetch();
        $showHorse = Utils::getHorses($pdo, Utils::getEventId(), Utils::getRaceNumber(), null, $show["horse_number"])[0];
//        var_dump($showHorse);
//        var_dump($show);

        $horses = Utils::getHorses($pdo, Utils::getEventId(), Utils::getRaceNumber());

        $notTop = array();
        foreach ($horses as $h) {
            if ($h["id"] !== $winHorse["id"] && $h["id"] !== $placeHorse["id"] && $h["id"] !== $showHorse["id"]) $notTop[] = $h;
        }

        $data = array();
        $data["race_event_id"] = Utils::getEventId();
        $data["race_race_number"] = Utils::getRaceNumber();
        $data["win"] = [$win["win_purse"], $win["place_purse"], $win["show_purse"]];
        $data["place"] = [$place["place_purse"], $place["show_purse"]];
        $data["show"] = [$show["show_purse"]];
        $data["top_horses"] = [$winHorse, $placeHorse, $showHorse];
        $data["other_horses"] = $notTop;
        Utils::sendResponse(200, $success=true, $msg=null, $data);
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {

    if (!Utils::validatePostRequestURLParams()) {
        Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
        exit;
    }

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

    $messages = array();
    (((isset($jsonData->horses) && !is_array($jsonData->horses)) || count($jsonData->horses) < 3 || count($jsonData->horses) > 3) ?
        $messages[] = "horses field must be an array of 3 horses" : false);
    if (count($messages) > 0) {
        Utils::sendResponse(400, $success = false, $msg = $messages, $data = null);
        exit;
    }

    $horses = $jsonData->horses;

    $message = $_SERVER['REQUEST_METHOD'] === 'POST' ? "Result created" : "Result Updated";
    $statusCd = $_SERVER['REQUEST_METHOD'] === 'POST' ? 201 : 200;

    try {
        // Unset result
        Utils::unsetResult($pdo, $horses[0]->race_event_id, $horses[0]->race_race_number);
        $hrs = [];
        foreach ($horses as $horse) {
            $horse = (array)$horse;
            if (isset($horse["can_be_deleted"])) unset($horse["can_be_deleted"]);

            $hrs[] = Utils::updateHorse($pdo, $horse);
        }

        $win = [$hrs[0]["horse_number"], $hrs[0]["win_purse"], $hrs[0]["place_purse"], $hrs[0]["show_purse"]];
        $place = [$hrs[1]["horse_number"], $hrs[1]["place_purse"], $hrs[1]["show_purse"]];
        $show = [$hrs[2]["horse_number"], $hrs[2]["show_purse"]];
        Utils::populateRaceStandingsTable($pdo, $horses[0]->race_event_id, $horses[0]->race_race_number, $win, $place, $show);

        Utils::sendResponse($statusCd, $success=true, $msg=[$message], $data=$hrs);
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Some horse object misses id field." . $ex], $data=null);
        exit;
    }
}

else {
    Utils::sendResponse(405, $success=false, $msg=["Method not allowed."], $data=null);
    exit;
}