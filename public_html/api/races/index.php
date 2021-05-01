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

            $horseQuery = "SELECT * FROM pick WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
            $horseStmt = $pdo->prepare($horseQuery);

            $races = array();
            // Get horses for each race
            foreach ($raceData["races"] as $race) {
                $stmt->execute(["race_event_id" => $race["event_id"], "race_race_number" => $race["race_number"]]);
                $horses = $stmt->fetchAll();
                $horsesVal = array();
                // Answer: Whether a horse can be deleted
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

