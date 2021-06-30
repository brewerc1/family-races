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

if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {

    if (Utils::getEventId() === null) {
        Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
        exit;
    }

    // Admin only View
    if (!Utils::isAdmin()) {
        Utils::sendResponse(403, $success=false, $msg=["Forbidden"], $data=null);
        exit;
    }

    try {
        Utils::populateEventStandingsTable($pdo, Utils::getEventId(), true);
        Utils::sendResponse(200, $success=true, $msg=["Results recalculated"], $data=null);
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Some horse object misses id field." . $ex], $data=null);
        exit;
    }
}
