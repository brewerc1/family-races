<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

use api\Utils;

include_once '../../Utils.php';

function validGetRequestURLParams(): bool
{
    return !isset($_GET['pg']) || is_numeric($_GET['pg']);
}


if (!Utils::isLoggedIn()) {
    Utils::sendResponse(401, $success = false, $msg = ["Authentication is required."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!validGetRequestURLParams()) {
        Utils::sendResponse(404, $success = false, $msg = ["Page not found"], $data = null);
        exit;
    }

    try {
        $eventId = $_GET["e"];
        $query = "SELECT race_number FROM race WHERE event_id=:eventId ORDER BY race_number DESC LIMIT 1";
        $options = ["eventId" => $eventId];
        $stmt = $pdo->prepare($query);
        $stmt->execute($options);
        $race = $stmt->fetch();

        if (!isset($race["race_number"])) {
            Utils::sendResponse(200, $success = true, $msg = ["No Races To Retrieve"], $data = []);
            exit;
        }

        $lastRace = $race["race_number"];

        $unfilteredResults = [];
        for ($raceId = 0; $raceId <= $lastRace; $raceId++) {
            $query = "SELECT race_race_number, horse_number, win_purse, place_purse, show_purse FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['race_event_id' => $eventId, 'race_race_number' => $raceId]);
            $horses = $stmt->fetchAll();
            array_push($unfilteredResults, [$raceId => $horses]);
        }

        $data = [];
        // Triple nested for loop is not a problem since this is a very small amount of data
        foreach ($unfilteredResults as $result) {
            $hasWin = false;
            $hasPlace = false;
            $hasShow = false;
            foreach ($result as $row) {
                foreach ($row as $horse) {
                    $isWin = key_exists("win_purse", $horse) && !is_null($horse["win_purse"])
                        && key_exists("place_purse", $horse) && !is_null($horse["place_purse"])
                        && key_exists("show_purse", $horse) && !is_null($horse["show_purse"]);

                    $isPlace = key_exists("place_purse", $horse) && !is_null($horse["place_purse"])
                        && key_exists("show_purse", $horse) && !is_null($horse["show_purse"]);

                    $isShow = key_exists("show_purse", $horse) && !is_null($horse["show_purse"]);

                    if ($isWin) $hasWin = true;
                    if ($isPlace && !$isWin) $hasPlace = true;
                    if ($isShow && !$isPlace && !$isWin) $hasShow = true;
                }
            }
            $hasResults = $hasWin && $hasPlace && $hasShow;
            array_push($data, $hasResults);
        }

        Utils::sendResponse(200, $success = true, $msg = ["All Event Race Result Statuses Retrieved"], $data = $data);
        exit;
    } catch (PDOException $ex) {
        Utils::sendResponse(500, $success = false, $msg = ["Server error: " . $ex], $data = null);
        exit;
    }
} else {
    Utils::sendResponse(405, $success = false, $msg = ["Method not allowed."], $data = null);
    exit;
}
