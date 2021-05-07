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
                $nextUrl = $_SERVER["SERVER_NAME"] . $endPoint  . "?" .  http_build_query($urlParams);
            }
            else $nextUrl = null;

            if ($page > 1) {
                $urlParams["pg"] = ($page - 1);
                $previousUrl = $_SERVER["SERVER_NAME"] . $endPoint . "?" . http_build_query($urlParams);
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

    public static function getHorses($pdo, $eventId, $raceNumber): array
    {

        $query = "SELECT * FROM horse WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number";
        $stmt = $pdo->prepare($query);

        $horseQuery = "SELECT * FROM pick WHERE race_event_id = :race_event_id AND race_race_number = :race_race_number AND horse_number = :horse_number";
        $horseStmt = $pdo->prepare($horseQuery);

        // Get horses for each race
        $stmt->execute(["race_event_id" => $eventId, "race_race_number" => $raceNumber]);
        $horses = $stmt->fetchAll();
        $horsesVal = array();

        // Answer: Whether horse can be deleted
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

        return $horsesVal;
    }

    public static function createAndGetHorses($pdo, $eventId, $raceNumber, $horses): array
    {
        $pdo->beginTransaction();
        $query = "INSERT INTO horse (race_event_id, race_race_number, horse_number) VALUES (:race_event_id, :race_race_number, :horse_number)";
        $stmt = $pdo->prepare($query);

        foreach ($horses as $horse) {
            $stmt->execute(["race_event_id" => $eventId, "race_race_number" => $raceNumber, "horse_number" => $horse]);
        }
        $pdo->commit();
        return self::getHorses($pdo, $eventId, $raceNumber);
    }

}