<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

use api\Utils;

include_once '../../Utils.php';

function validGetRequestURLParams(): bool
{
    return !isset($_GET['pg']) || is_numeric($_GET['pg']);
}


if(!Utils::isLoggedIn()) {
    Utils::sendResponse(401, $success=false, $msg=["Authentication is required."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!validGetRequestURLParams()) {
        Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
        exit;
    }
    try {
        $eventId = $_GET["e"];
        $query = "SELECT * FROM pick WHERE race_event_id=:eventId";
        $options = ["eventId" => $eventId];
        $stmt = $pdo->prepare($query);
        $stmt->execute($options);
        Utils::sendResponse(200, $success=true, $msg=["All Event Race Results Retrieved"], $data=$stmt->fetchAll());
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: " . $ex], $data=null);
        exit;
    }
}

else {
    Utils::sendResponse(405, $success=false, $msg=["Method not allowed."], $data=null);
    exit;
}
