<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Testing only
$_SESSION['id'] = '1';
$_SESSION['admin'] = 1;

use api\Response;
include_once '../Response.php';

// Helper Functions

function sendResponse($statusCode, $success=false, $msg=null, $data=null) {
    $response = new Response();
    $response->setHttpStatusCode($statusCode);
    $response->setSuccess($success);

    if ($msg !== null)
        foreach ($msg as $m)
            $response->AddMessages($m);

    if ($data !== null)
        $response->setData($data);

    $response->send();
}

function isAdmin() {
    return $_SESSION['admin'] === 1;
}

function isValidContentType() {
    return $_SERVER['CONTENT_TYPE'] === 'application/json';
}

// Check: if Logged in.
if(empty($_SESSION["id"])) {
    sendResponse(401, $success=false, $msg=["Authentication is required."]);
    exit;
}

$_eventId = 0;
if (array_key_exists('e', $_GET) && is_numeric($_GET['e']))
    $_eventId = $_GET['e'];


function validGetRequestURLParams() {
    return ((count($_GET) == 1) && !empty($_GET['pg']) && is_numeric($_GET['pg'])) || empty($_GET);
}

function validPostRequestURLParams() {
    return ((count($_GET) == 1) && !empty($_GET['e']) && is_numeric($_GET['e']));
}

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
            sendResponse(404, $success=false, $msg=["Page not found."], $data=null);
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
        sendResponse(200, $success=true, $msg=null, $data=$eventData);
        exit;
    }
    catch (PDOException $ex) {
        sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET)) {
    try {
        // Admin only View
        if (!isAdmin()) {
            sendResponse(403, $success=false, $msg=["Forbidden"], $data=null);
            exit;
        }

        if (!isValidContentType()) {
            sendResponse(400, $success=false, $msg=["Content type must be set to application/json"], $data=null);
            exit;
        }

        $postData = file_get_contents('php://input');
        if (!$jsonData = json_decode($postData)) {
            sendResponse(400, $success=false, $msg=["Request body is not valid JSON."], $data=null);
            exit;
        }

        if (empty($jsonData->name) || empty($jsonData->date) || empty($jsonData->pot)) {
            $messages = array();
            (empty($jsonData->name) ? $messages[] = "Name field is required and can't be blank" : false);
            (empty($jsonData->date) ? $messages[] = "Date field is required and can't be blank" : false);
            (empty($jsonData->pot) ? $messages[] = "Pot field is required and can't be blank" : false);
            sendResponse(400, $success=false, $msg=$messages, $data=null);
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
            sendResponse(400, $success=false, $msg=$messages, $data=null);
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

        sendResponse(201, $success=true, $msg=["Event created"], $data=$stmt->fetchAll());
        exit;
    } catch (PDOException $ex) {
        sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }

}
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && (isset($_GET['e']) && is_numeric($_GET['e']))) {
    // Admin only
    if ($_SESSION['admin'] !== 1) {
        $response = new Response();
        $response->setHttpStatusCode(403);
        $response->setSuccess(false);
        $response->AddMessages("Forbidden");
        $response->send();
        exit;
    }

    // More logic
}
elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH' && (isset($_GET['e']) && is_numeric($_GET['e']))) {
    // Admin only
    if ($_SESSION['admin'] !== 1) {
        $response = new Response();
        $response->setHttpStatusCode(403);
        $response->setSuccess(false);
        $response->AddMessages("Forbidden");
        $response->send();
        exit;
    }

    // More logic
}
//elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && canManipulateEvent()) {}
else {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->AddMessages("Method not allowed.");
    $response->send();
    exit;
}
