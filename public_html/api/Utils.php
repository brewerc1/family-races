<?php


namespace api;
include_once 'Response.php';

class Utils
{
    public static function sendResponse($statusCode, $success=false, $msg=null, $data=null) {
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

    public static function isAdmin(): bool
    {
        return !empty($_SESSION['admin']) && $_SESSION['admin'] === 1;
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION["id"]);
    }

    public static function getEventId(): ?int
    {
        return isset($_GET['e']) && is_numeric($_GET['e']) ? intval($_GET['e']) : null;
    }

    public static function getRaceNumber(): ?int
    {
        return isset($_GET['r']) && is_numeric($_GET['r']) ? intval($_GET['r']) : null;
    }

    public static function isValidContentType(): bool
    {
        return isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json';
    }

    public static function getPageNumber(): int
    {
        return array_key_exists('pg', $_GET) && is_numeric($_GET['pg']) ? intval($_GET['pg']) : 1;
    }

    public static function getWithPagination($pdo, $tableName, $page, $endPoint, $keyWord,
                                             $customQuery=null, $urlQuery=null, $customOptions=null,
                                             $customQueryTotalPage=null, $customOptionPage=null): array
    {
        // Number of events per page
        $pageLimit = 10;

        // Validate the page
        $query = $customQueryTotalPage === null ? "SELECT COUNT(*) AS total FROM " . $tableName : $customQueryTotalPage;
        $total = 1;
        if (strlen($query) > 0) {
            $stmt = $pdo->prepare($query);
            $opt = $customOptionPage === null ? [] : $customOptionPage;
            $stmt->execute($opt);
            $total = intval($stmt->fetch()["total"]);
        }
        $numberOfPage = ceil($total / $pageLimit);
        $numberOfPage = $numberOfPage == 0 ? 1 : $numberOfPage;

        // Page not found.
        if ($page > $numberOfPage) {
            $returnData["pageNotFound"] = "Page not found.";
        }
        else {
            $offset = $page == 1 ? 0 : ($pageLimit * ($page - 1));

            // Pagination urls
            $params = "?";
            if ($urlQuery !== null) {
                foreach ($urlQuery as $key => $val) {
                    $params .= $key . "=" . $val . "&";
                }
                $params .= "pg=";
            } else {
                $params = "?pg=";
            }

            $nextUrl = ($page < $numberOfPage) ? $_SERVER["SERVER_NAME"] . $endPoint . $params . ($page + 1) : null;
            $previousUrl = $page > 1 ? $_SERVER["SERVER_NAME"] . $endPoint . $params . ($page - 1) : null;

            // Get all event
            $query = $customQuery === null ? "SELECT * FROM " . $tableName . " LIMIT :_limit OFFSET :off_set" : $customQuery;
            $options = $customOptionPage !== null && count($customOptionPage) == 0 ? [] : ['_limit' => $pageLimit, 'off_set' => $offset];
            $options = $customOptions === null ? $options : array_merge($options, $customOptions);
            $stmt = $pdo->prepare($query);
            $stmt->execute($options);

            // Fetching
            $data = $stmt->fetchAll();

            $rowReturned = count($data);
//            if ($rowReturned < $pageLimit || isset($_GET['e'])) $nextUrl = null;

            $returnData = [
                'rowReturned' => $rowReturned,
                'numberOfPages' => $numberOfPage,
                'next' => $nextUrl,
                'previous' => $previousUrl,
                $keyWord => $data
            ];
        }

        return $returnData;
    }


    /**
     * @param $pdo
     * @return bool
     *
     * True is returned if there is an open event in DB
     */
    public static function dbHasAnOpenEvent($pdo): bool {
        $query = "SELECT * FROM event WHERE status = 0";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) return true;

        return false;
    }

    public static function getAllWithPagination($pdo, $endPoint, $keyWord,
                                             $query, $pageQuery, $optionsForQuery, $optionForPageQuery, $urlParams=[]): array
    {

        // Number of items per page (Default)
        $pageLimit = 10;

        // Validate the page
        $stmt = $pdo->prepare($pageQuery);
        $stmt->execute($optionForPageQuery);
        $total = intval($stmt->fetch()["total"]);
        $numberOfPage = ceil($total / $pageLimit);
        $numberOfPage = $numberOfPage == 0 ? 1 : $numberOfPage;

        $page = self::getPageNumber();

        // Page not found.
        if ($page > $numberOfPage) {
            $returnData["pageNotFound"] = "Page not found.";
        }
        else {
            $offset = $page == 1 ? 0 : ($pageLimit * ($page - 1));

            // Pagination urls
            if ($page < $numberOfPage) {
                $urlParams["pg"] = ($page + 1);
                //$nextUrl = $_SERVER["SERVER_NAME"] . $endPoint  . "?" .  http_build_query($urlParams);
                $nextUrl = $endPoint  . "?" .  http_build_query($urlParams);
            }
            else $nextUrl = null;

            if ($page > 1) {
                $urlParams["pg"] = ($page - 1);
                //$previousUrl = $_SERVER["SERVER_NAME"] . $endPoint . "?" . http_build_query($urlParams);
                $previousUrl = $endPoint  . "?" .  http_build_query($urlParams);
            }
            else $previousUrl = null;

            // Get all
            $stmt = $pdo->prepare($query);
            $stmt->execute(array_merge($optionsForQuery, ['_limit' => $pageLimit, 'off_set' => $offset]));
            $data = $stmt->fetchAll();
            $rowReturned = count($data);

            $returnData = [
                'rowReturned' => $rowReturned,
                'numberOfPages' => $numberOfPage,
                'next' => $nextUrl,
                'previous' => $previousUrl,
                $keyWord => $data
            ];
        }

        return $returnData;
    }

    public static function getHorses($pdo, $eventId=null, $raceNumber=null, $horseId=null, $horseName=null): array
    {
        // TODO: Support to get a single horse

//        if ($eventId === null && $raceNumber !== null) return [];

        $horsesWihPagination = null;

        // GET one horse by id
        if ($eventId === null && $raceNumber === null && $horseId !== null) {
            $query = "SELECT * FROM horse WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $options = ["id" => $horseId];
            $stmt->execute($options);
            $horses = $stmt->fetchAll();
        }

        // GET one horse by the horse number
        elseif ($eventId !== null && $raceNumber !== null && $horseName !== null) {
            $query = "SELECT * FROM horse WHERE horse_number = :horse_number AND race_event_id = :race_event_id AND race_race_number = :race_race_number";
            $stmt = $pdo->prepare($query);
            $options = ["horse_number" => $horseName, "race_event_id" => $eventId, "race_race_number" => $raceNumber];
            $stmt->execute($options);
            $horses = $stmt->fetchAll();
        }

        // TODO:
        elseif ($eventId !== null && $raceNumber !== null && $horseId == null) {
            $query = "SELECT * FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
            $stmt = $pdo->prepare($query);
            $options = ["race_event_id" => $eventId, "race_race_number" => $raceNumber];
            $stmt->execute($options);
            $horses = $stmt->fetchAll();
        }
        // GET LOTS OF HORSES WITH Pagination
        elseif ($eventId !== null && $raceNumber === null && $horseId === null) {
            $query = "SELECT * FROM horse WHERE race_event_id = :race_event_id LIMIT :_limit OFFSET :off_set";
            $options = ["race_event_id" => $eventId];
            $pageQuery = "SELECT COUNT(*) AS total FROM horse WHERE race_event_id = :race_event_id";
            $optionPage = ["race_event_id" => $eventId];
            $urlParams = ["e" => $eventId];
            $horsesWihPagination = self::getAllWithPagination($pdo, "/api/horses/", "horses", $query, $pageQuery, $options, $optionPage, $urlParams);
            $horses = $horsesWihPagination["horses"];
        }
        else {
            $query = "SELECT * FROM horse LIMIT :_limit OFFSET :off_set";
            $options = [];
            $pageQuery = "SELECT COUNT(*) AS total FROM horse";
            $optionPage = [];
            $horsesWihPagination = self::getAllWithPagination($pdo, "/api/horses/", "horses", $query, $pageQuery, $options, $optionPage);
            $horses = $horsesWihPagination["horses"];
        }

        $horseQuery = "SELECT * FROM pick WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
        $horseStmt = $pdo->prepare($horseQuery);
        // Get horses for each race
        $horsesVal = array();
        // Answer: Whether horse can be deleted
        foreach ($horses as $horse) {
            $horseStmt->execute(["race_event_id" => $horse["race_event_id"],
                "race_race_number" => $horse["race_race_number"], "horse_number" => $horse["horse_number"]]);

            if ($horseStmt->rowCount() > 0) {
                $horse["can_be_deleted"] = false;
            } else {
                $horse["can_be_deleted"] = true;
            }
            $horsesVal[] = $horse;
        }

        // Return horses with pagination
        if ($horsesWihPagination !== null) {
            $horsesWihPagination["horses"] = $horsesVal;
            return $horsesWihPagination;
        }

        // Return only horses
        return $horsesVal;
    }

    public static function createAndGetHorses($pdo, $eventId, $raceNumber, $horses): array
    {
        $pdo->beginTransaction();
        $query = "INSERT INTO horse (race_event_id, race_race_number, horse_number) VALUES (:race_event_id, :race_race_number, :horse_number)";
        $stmt = $pdo->prepare($query);

        // Check if there is already a horse with the same name for this race and event
        $checkQuery = "SELECT * FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
        $stmtCheckQuery = $pdo->prepare($checkQuery);

        foreach ($horses as $horse) {
            $stmtCheckQuery->execute(["race_event_id" => $eventId, "race_race_number" => $raceNumber, "horse_number" => $horse]);
            if ($stmtCheckQuery->rowCount() === 0)
                $stmt->execute(["race_event_id" => $eventId, "race_race_number" => $raceNumber, "horse_number" => $horse]);
        }
        $pdo->commit();
        return self::getHorses($pdo, $eventId, $raceNumber);
    }

    public static function validGetRequestURLParams(): bool
    {
        if (count($_GET) > 2) return false;
        if (isset($_GET['pg']) && !is_numeric($_GET['pg'])) return false;
        if (isset($_GET['e']) && !is_numeric($_GET['e'])) return false;
        if (isset($_GET['r']) && !is_numeric($_GET['r']) && !isset($_GET['e'])) return false;
        return true;
    }

    public static function validatePostRequestURLParams(): bool
    {
        return empty($_GET);
    }

    public static function updateHorse($pdo, $horse) {

        $options = array();
//        $options["race_event_id"] = $horse["race_event_id"];
//        $options["race_race_number"] = $horse["race_race_number"];
        $options["id"] = $horse["id"];

        if (isset($horse["race_event_id"])) unset($horse["race_event_id"]);
        if (isset($horse["race_race_number"])) unset($horse["race_race_number"]);
        if (isset($horse["can_be_deleted"])) unset($horse["can_be_delete"]);
        unset($horse["id"]);

        $update = "";

        foreach ($horse as $key => $val) {
            if ($val !== null) {
                $update .= $key . " =:" . $key . ",";
                $options[$key] = $val;
            }
        }
        $update = (substr($update, -1) === ',') ? substr($update, 0, -1) : $update;
//        echo $update . "\n";
//        var_dump($options);
//        exit;

        $pdo->beginTransaction();
        $query = "UPDATE horse SET " . $update . " WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute($options);
        $pdo->commit();

//        $query = "SELECT * FROM horse  WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND id = :id";
//        $stmt = $pdo->prepare($query);
//        $stmt->execute(["race_event_id" => $options["race_event_id"], "race_race_number" => $options["race_race_number"], "id" => $options["id"]]);

        return self::getHorses($pdo, null, null, $options["id"])[0];
    }

    public static function populateRaceStandingsTable($pdo, $event_id, $race_number, $win, $place, $show) {

        $pdo->beginTransaction(); // Start transaction

        //
        $query = "DELETE FROM race_standings WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number]);

        $query = "SELECT user_id, horse_number, finish FROM pick WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['race_event_id' => $event_id, 'race_race_number' => $race_number]);
        $picks = $stmt->fetchAll();

        $insert_race_standings_sql = "INSERT INTO `race_standings` (race_event_id, race_race_number, user_id, earnings) VALUES (?, ?, ?, ?)";
        $insert_race_standings = $pdo->prepare($insert_race_standings_sql);

        foreach ($picks as $pick) {

            if ($pick['horse_number'] === $win[0]) {

                if ($pick['finish'] === 'win')
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], $win[1]]);

                elseif ($pick['finish'] === 'place')
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], $win[2]]);

                elseif ($pick['finish'] === 'show')
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], $win[3]]);
            }

            elseif ($pick['horse_number'] === $place[0]) {

                if ($pick['finish'] === 'place')
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], $place[1]]);

                elseif ($pick['finish'] === 'show')
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], $place[2]]);

                else
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], 0.00]);

            }

            elseif ($pick['horse_number'] === $show[0]) {

                if ($pick['finish'] === 'show')
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], $show[1]]);

                else
                    $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], 0.00]);

            }

            else {
                $insert_race_standings->execute([$event_id, $race_number, $pick['user_id'], 0.00]);
            }

        }
        $pdo->commit();
    }
    public static function populateEventStandingsTable($pdo, $event_id, $recalculate=false) {
        $pdo->beginTransaction(); // Start transaction
        if ($recalculate) {
            $query = "DELETE FROM event_standings WHERE event_id = :event_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['event_id' => $event_id]);
        }

        $query = "SELECT user_id, sum(earnings) as total FROM race_standings WHERE race_event_id = :race_event_id GROUP BY user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['race_event_id' => $event_id]);

        if ($stmt->rowCount() > 0) { // If event has records
            $race_standings = $stmt->fetchAll();
            $query = "INSERT INTO event_standings (event_id, user_id, earnings) VALUES (:event_id, :user_id, :earnings)";
            $stmt = $pdo->prepare($query);
            $winner = array('total' => 0);
            foreach ($race_standings as $standing) {
                $uid = $standing['user_id'];
                $earnings = floatval($standing['total']);
                $stmt->execute(['event_id' => $event_id, 'user_id' => $uid, 'earnings' => $earnings]);

                if ($earnings > floatval($winner['total']))
                    $winner = $standing;
            }
            $query = "UPDATE event SET status = :status, champion_id = :champion_id, champion_purse = :champion_purse, champion_photo = :champion_photo WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['status' => 1, 'champion_id' => $winner['user_id'], 'champion_purse' => $winner['total'], 'champion_photo' => '', 'id' => $event_id]);

        }
        else { // If event has no record
            // Change event Status
            $query = "UPDATE event SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['status' => 1, 'id' => $event_id]);

            // Close all races that belong to this event
            $query = "SELECT race_number FROM race WHERE event_id = :event_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['event_id' => $event_id]);
            $races = $stmt->fetchAll();
            foreach($races as $race) {
                $race_number = $race["race_number"];
                $query = "UPDATE race SET window_closed = 1 WHERE event_id = :event_id AND race_number = :race_number";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['event_id' => $event_id, 'race_number' => $race_number]);
            }
        }
        $pdo->commit(); // Write all changes into DB
    }

    public static function unsetResult($pdo, $race_event_id, $race_race_number) {
        $pdo->beginTransaction(); // Start transaction
        $query = "UPDATE horse SET finish = NULL, win_purse = NULL, place_purse = NULL, show_purse = NULL WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND finish IS NOT NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute(["race_event_id" => $race_event_id, "race_race_number" => $race_race_number]);
        $pdo->commit(); // Write all changes into DB
    }

}