<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// Testing only
$_SESSION['id'] = '1';
$_SESSION['admin'] = 1;

use api\Response;
include_once '../Response.php';

// unused code
function canManipulateEvent() {
    return $_SESSION["admin"] == 1 && (isset($_GET['e']) && is_numeric($_GET['e']));
}

// Check: if Logged in.
if(empty($_SESSION["id"])) {
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->AddMessages("Authentication is required.");
    $response->send();
    exit;
}

$_eventId = 0;
if (array_key_exists('e', $_GET) && is_numeric($_GET['e']))
    $_eventId = $_GET['e'];

$page = 1;
if (array_key_exists('pg', $_GET) && is_numeric($_GET['pg']))
    $page = $_GET['pg'];


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    try {

        $pageLimit = 5; // Number of events per page
        $offset = $page == 1 ? 0 : ($pageLimit * ($page - 1));

        // Pagination urls
        $nextUrl = $_SERVER["SERVER_NAME"] . '/api/events/?pg=' . ($page + 1);
        $previousUrl = $page > 1 ? $_SERVER["SERVER_NAME"] . '/api/events/?pg=' . ($page - 1) : null;


        if ($_eventId == 0) { // All events
            $query = "SELECT * FROM event LIMIT :_limit OFFSET :off_set";
            $options = ['_limit' => $pageLimit, 'off_set' => $offset];
        }
        else { // Single event
            $query = "SELECT * FROM event WHERE id = :id";
            $options = ['id'=> $_eventId];
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($options);

        // Fetching
        if ($stmt->rowCount() == 1) $data = $stmt->fetch();
        else $data = $stmt->fetchAll();

        $rowReturned = count($data);
        if ($rowReturned < $pageLimit || isset($_GET['e'])) $nextUrl = null;

        // Success response
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $eventData = [
            'rowReturned' => $rowReturned,
            'next' => $nextUrl,
            'previous' => $previousUrl,
            'events' => $data
        ];
        $response->setData($eventData);
        $response->send();
        exit;
    }
    catch (PDOException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->AddMessages("Server error: ");
        $response->send();
        exit;
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Admin only
    if ($_SESSION['admin'] !== 1) {
        $response = new Response();
        $response->setHttpStatusCode(403);
        $response->setSuccess(false);
        $response->AddMessages("Forbidden");
        $response->send();
        exit;
    }

    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->AddMessages("Content type must be set to application/json");
        $response->send();
        exit;
    }

    $postData = file_get_contents('php://input');
    if (!$jsonData = json_decode($postData)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->AddMessages("Request body is not valid JSON.");
        $response->send();
        exit;
    }

    if (empty($jsonData->name) || empty($jsonData->date) || empty($jsonData->pot)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        (empty($jsonData->name) ? $response->AddMessages("Name field is required") : false);
        (empty($jsonData->date) ? $response->AddMessages("Date field is required") : false);
        (empty($jsonData->pot) ? $response->AddMessages("Pot field is required") : false);
        $response->send();
        exit;
    }

    // Validate input type

    $response = new Response();
    $response->setHttpStatusCode(201);
    $response->setSuccess(true);
    $response->AddMessages("Event created.");
    $response->send();
    exit;
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
