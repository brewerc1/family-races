<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Testing only
$_SESSION['id'] = '1';
$_SESSION['admin'] = 1;

use api\Utils;
include_once '../Utils.php';

function validGetRequestURLParams() {
    return ((count($_GET) == 1) && !empty($_GET['pg']) && is_numeric($_GET['pg'])) || empty($_GET);
}


if(!Utils::isLoggedIn()) {
    Utils::sendResponse(401, $success=false, $msg=["Authentication is required."]);
    exit;
}

// GET ALL EVENTS
if ($_SERVER['REQUEST_METHOD'] === 'GET' && validGetRequestURLParams()) {

    try {
        // Page number
        $page = array_key_exists('pg', $_GET) && is_numeric($_GET['pg']) ? $_GET['pg'] : 1;

        // Number of events per page
        $pageLimit = 5;

        // Validate the page
        $query = "SELECT COUNT(*) AS totalEvent FROM event";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $totalEvent = intval($stmt->fetch()["totalEvent"]);
        $numberOfPage = ceil($totalEvent / $pageLimit);
        $numberOfPage = $numberOfPage == 0 ? 1 : $numberOfPage;

        // Page not found.
        if ($page > $numberOfPage) {
            Utils::sendResponse(404, $success=false, $msg=["Page not found."], $data=null);
            exit;
        }

        $offset = $page == 1 ? 0 : ($pageLimit * ($page - 1));

        // Pagination urls
        $nextUrl = ($page < $numberOfPage) ? $_SERVER["SERVER_NAME"] . '/api/events/?pg=' . ($page + 1) : null;
        $previousUrl = $page > 1 ? $_SERVER["SERVER_NAME"] . '/api/events/?pg=' . ($page - 1) : null;

        // Get all event
        $query = "SELECT * FROM event LIMIT :_limit OFFSET :off_set";
        $options = ['_limit' => $pageLimit, 'off_set' => $offset];
        $stmt = $pdo->prepare($query);
        $stmt->execute($options);

        // Fetching
        $data = $stmt->fetchAll();

        $rowReturned = count($data);
        if ($rowReturned < $pageLimit || isset($_GET['e'])) $nextUrl = null;

        $eventData = [
            'rowReturned' => $rowReturned,
            'numberOfPages' => $numberOfPage,
            'next' => $nextUrl,
            'previous' => $previousUrl,
            'events' => $data
        ];
        Utils::sendResponse(200, $success=true, $msg=null, $data=$eventData);
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }
}

// CREATE AN EVENT
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET)) {
    try {
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

        if (empty($jsonData->name) || empty($jsonData->date) || empty($jsonData->pot)) {
            $messages = array();
            (empty($jsonData->name) ? $messages[] = "Name field is required and can't be blank" : false);
            (empty($jsonData->date) ? $messages[] = "Date field is required and can't be blank" : false);
            (empty($jsonData->pot) ? $messages[] = "Pot field is required and can't be blank" : false);
            Utils::sendResponse(400, $success=false, $msg=$messages, $data=null);
            exit;
        }

        // Validate input type
        $eventName = $jsonData->name;
        $eventNameErr = (strlen($eventName) > 25) ? "Event name field length must be no more than 25 chars" : "";
        $date = $jsonData->date;
        $dateErr = (date('Y-m-d', strtotime($date)) == $date) ? "" : "Date field must be a valid date (YYYY-MM-DD)";
        $pot = $jsonData->pot;
        $potErr = (!is_numeric($pot) || strlen($pot) > 6) ? "Pot field must be int with no more than 6 digits" : "";

        if (!empty($eventNameErr) || !empty($potErr) || !empty($dateErr)) {
            $messages = array();
            (!empty($eventNameErr) ? $messages[] = $eventNameErr : false);
            (!empty($dateErr) ? $messages[] = $dateErr : false);
            (!empty($potErr) ? $messages[] = $potErr : false);
            Utils::sendResponse(400, $success=false, $msg=$messages, $data=null);
            exit;
        }

        // Create the new event
        $pdo->beginTransaction();
        $query = "INSERT INTO event (name, date, pot) VALUES (:name, :date, :pot)";
        $options = ['name' => $eventName, 'date' => $date, 'pot' => $pot];
        $stmt = $pdo->prepare($query);
        $stmt->execute($options);
        $createdEventId = $pdo->lastInsertId();
        $pdo->commit();

        $query = "SELECT * FROM event WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $createdEventId]);

        Utils::sendResponse(201, $success=true, $msg=["Event created"], $data=$stmt->fetchAll());
        exit;
    } catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }

}

// UPDATE AN EVENT
elseif (($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') && (Utils::getEventId() !== null)) {
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

    $eventId = Utils::getEventId();

    // Validate inputs
    $errMessages = array();
    $name = isset($jsonData->name) ? $jsonData->name : null;
    (($name !== null && (strlen($name) <= 0 || strlen($name) > 25)) ?
        $errMessages[] = "Event name field length must be no more than 25 chars" : false);

    $date = isset($jsonData->date) ? $jsonData->date : null;
    (($date !== null && (date('Y-m-d', strtotime($date)) !== $date)) ?
        $errMessages[] = "Date field must be a valid date (YYYY-MM-DD)" : false);

    $pot = isset($jsonData->pot) ? $jsonData->pot : null;
    ($pot !== null && !is_numeric($pot) || (strlen($pot) > 6) ?
        $errMessages[] = "Pot field must be int with no more than 6 digits" : false);

    $status = isset($jsonData->status) ? $jsonData->status : null;
    ($status !== null && !($status === 1 || $status === 0) ?
        $errMessages[] = "Status must be either 0 (open) or 1 (close)." : false);

    $championId = isset($jsonData->champion_id) ? $jsonData->champion_id : null;
    (($championId !== null && !is_numeric($championId) || (strlen($championId) > 6)) ?
        $errMessages[] = "Champion ID must be int no more than 6 digits." : false);

    $championPurse = !empty($jsonData->champion_purse) ? $jsonData->champion_purse : null;
    (($championPurse !== null && !is_double($championPurse) && strlen($championId) > 6) ?
        $errMessages[] = "Champion PURSE must be int/double/float no more than 6 digits." : false);

    // MUST REVIEW the validation for this one
    $championPhoto = isset($jsonData->champion_photo) ? $jsonData->champion_photo : null;
    (($championPhoto !== null && (strlen($championPhoto) < 1 || strlen($championPhoto) > 128)) ?
        $errMessages[] = "Champion PHOTO must be string no more than 128 chars." : false);

    // Send any error messages
    if (count($errMessages) > 0) {
        Utils::sendResponse(400, $success=false, $msg=$errMessages, $data=null);
        exit;
    }

    try {
        $subQuery = "";
        $options = array();

        if ($name !== null) {
            $subQuery .= " name = :name,";
            $options["name"] = $name;
        }

        if ($date !== null) {
            $subQuery .= " date = :date,";
            $options["date"] = $date;
        }

        if ($pot !== null) {
            $subQuery .= " pot = :pot,";
            $options["pot"] = $pot;
        }

        if ($status !== null) {
            $subQuery .= " status = :status,";
            $options["status"] = $status;
        }

        if ($championId !== null) {
            $subQuery .= " champion_id = :champion_id,";
            $options["champion_id"] = $championId;
        }

        if ($championPurse !== null) {
            $subQuery .= " champion_purse = :champion_purse,";
            $options["champion_purse"] = $championPurse;
        }

        if ($championPhoto !== null) {
            $subQuery .= " champion_photo = :champion_photo,";
            $options["champion_photo"] = $championPhoto;
        }

        if (count($options) > 0) {
            $options["id"] = $eventId;
            $pdo->beginTransaction();
            $query = "UPDATE event SET " . substr($subQuery, 0, -1) . " WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute($options);
            $pdo->commit();

            $query = "SELECT * FROM event WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(["id" => $eventId]);

            Utils::sendResponse(200, $success=true, $msg=["Event updated"], $data=$stmt->fetchAll());
            exit;
        }

    } catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }

}
//elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {}

// ANY UNSUPPORTED OPERATION
else {
    Utils::sendResponse(405, $success=false, $msg=["Method not allowed."], $data=null);
    exit;
}
