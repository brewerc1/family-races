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

//        for ($i = 0; $i < count($horses); $i++) {
//            $horse = $horses[$i];
//            if ($horse['id'] === $winHorse['id'] || $horse['id'] === $placeHorse['id'] || $horse['id'] === $showHorse['id']) {
//                unset($horses[$i]);
//            }
//        }
//        var_dump($horses);

        $data = array();
        $data["race_event_id"] = Utils::getEventId();
        $data["race_race_number"] = Utils::getRaceNumber();
        $data["win"] = [$win["win_purse"], $win["place_purse"], $win["show_purse"]];
        $data["place"] = [$place["place_purse"], $place["show_purse"]];
        $data["show"] = [$show["show_purse"]];
        $data["top_horses"] = [$winHorse, $placeHorse, $showHorse];
        $data["horses"] = $horses;
        Utils::sendResponse(200, $success=true, $msg=null, $data);
        exit;
    }
    catch (PDOException $ex) {
        Utils::sendResponse(500, $success=false, $msg=["Server error: "], $data=null);
        exit;
    }
}
else {
    Utils::sendResponse(405, $success=false, $msg=["Method not allowed."], $data=null);
    exit;
}