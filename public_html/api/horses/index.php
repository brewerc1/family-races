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

// TODO: Add validGetRequestURLParams()

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//    if (!Utils::validGetRequestURLParams()) {
//        Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
//        exit;
//    }
    try {
        // GET ALL horses
        $horsesData = Utils::getHorses($pdo, Utils::getEventId(), Utils::getRaceNumber());
        Utils::sendResponse(200, $success=true, $msg=null, $data=$horsesData);
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Required data && Optional data
    $messages = array();
    // Required
    (empty($jsonData->race_event_id) || !is_numeric($jsonData->race_event_id) ?
        $messages[] = "race_event_id field is required and must be numeric" : false);

    (empty($jsonData->race_race_number) || !is_numeric($jsonData->race_race_number) ?
        $messages[] = "race_race_number field is required and must be numeric" : false);

    // Optionals

    ((isset($jsonData->horses) && !is_array($jsonData->horses)) ?
        $messages[] = "horses field must be an array of string" : false);

    if (count($messages) > 0) {
        Utils::sendResponse(400, $success = false, $msg = $messages, $data = null);
        exit;
    }

    $eventId = $jsonData->race_event_id;
    $raceNumber = $jsonData->race_race_number;
    //Optionals
    $horses = $jsonData->horses ?? [];

    Utils::sendResponse(201, $success = true, $msg = ["All horses created"], $data = Utils::createAndGetHorses($pdo, $eventId, $raceNumber, $horses));
    exit;
}

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {

    if (!(count($_GET) === 0)) {
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

    // Optionals fields
    $messages = [];
    ((isset($jsonData->horses) && !is_array($jsonData->horses)) ?
        $messages[] = "horses field must be an array of horse (object or IDs)" : false);
    if (count($messages) > 0) {
        Utils::sendResponse(400, $success = false, $msg = $messages, $data = null);
        exit;
    }

    $horses = $jsonData->horses;

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            try {
                // TODO: Check if this horse can be deleted
                $pdo->beginTransaction();
                $query = "DELETE FROM horse WHERE id = :id";
                $stmt = $pdo->prepare($query);

                foreach ($horses as $horse)
                    $stmt->execute(['id' => $horse->id]);
                $pdo->commit();
                Utils::sendResponse(204, $success=true, $msg=null, $data=null);
                exit;
            }
            catch (PDOException $ex) {
                Utils::sendResponse(500, $success=false, $msg=["Some horse object misses id field."], $data=null);
                exit;
            }
    }

    // PUT/PATCH
    // required
//    "id",
//    "race_event_id",
    // Optionals
//    "race_race_number",
//    "horse_number"
    try {
        $hrs = array();
        foreach ($horses as $horse) {
            $horse = (array)$horse;
            if (isset($horse["can_be_deleted"]))
                unset($horse["can_be_deleted"]);

            $hrs[] = Utils::updateHorse($pdo, $horse);
        }

        Utils::sendResponse(200, $success=true, $msg=["Horses updated"], $data=$hrs);
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Some horse object misses id field."], $data=null);
        exit;
    }

}
//elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
//
//    if (!(isset($_GET['h']) && is_numeric($_GET['h']) && count($_GET) === 1)) {
//        Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
//        exit;
//    }
//
//    // Admin only View
//    if (!Utils::isAdmin()) {
//        Utils::sendResponse(403, $success=false, $msg=["Forbidden"], $data=null);
//        exit;
//    }
//
//    // TODO: Verify that there is a horse with id h
//    // localhost/api/horses/?h=1
//
//    try {
//
//        // TODO: Check if this horse can be deleted
//
//        $query = "DELETE FROM horse WHERE id = :id";
//        $stmt = $pdo->prepare($query);
//        $stmt->execute(['id' => $_GET['h']]);
//        Utils::sendResponse(204, $success=true, $msg=null, $data=null);
//        exit;
//    }
//    catch (PDOException $ex) {
//        Utils::sendResponse(500, $success=false, $msg=["Server error"], $data=null);
//        exit;
//    }
//}

else {
    Utils::sendResponse(405, $success=false, $msg=["Method not allowed."], $data=null);
    exit;
}