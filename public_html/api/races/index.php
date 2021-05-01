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
    return ((count($_GET) == 1) && !empty($_GET['pg']) && is_numeric($_GET['pg'])) || empty($_GET);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && validGetRequestURLParams()) {
    try {
        $page = Utils::getPageNumber();
        $raceData = Utils::getWithPagination($pdo, "race", $page, "/api/races/", "races");

        if (key_exists("pageNotFound", $raceData)) {
            Utils::sendResponse(404, $success=false, $msg=["Page not found"], $data=null);
        }
        else {
            $query = "SELECT * FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
            $stmt = $pdo->prepare($query);

            $races = array();
            foreach ($raceData["races"] as $race) {
                $stmt->execute(["race_event_id" => $race["event_id"], "race_race_number" => $race["race_number"]]);
                $race["horses"] = $stmt->fetchAll();
                $races[] = $race;
            }

            $raceData["races"] = $races;
            Utils::sendResponse(200, $success=true, $msg=null, $data=$raceData);
        }
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: " . $ex], $data=null);
        exit;
    }
}

// ANY UNSUPPORTED OPERATION
else {
    Utils::sendResponse(405, $success=false, $msg=["Method not allowed."], $data=null);
    exit;
}

