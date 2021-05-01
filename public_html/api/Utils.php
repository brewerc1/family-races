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

    public static function isAdmin() {
        return !empty($_SESSION['admin']) && $_SESSION['admin'] === 1;
    }

    public static function isLoggedIn() {
        return !empty($_SESSION["id"]);
    }

    public static function getEventId() {
        return isset($_GET['e']) && is_numeric($_GET['e']) ? $_GET['e'] : null;
    }

    public static function isValidContentType() {
        return isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json';
    }

    public static function getPageNumber() {
        return array_key_exists('pg', $_GET) && is_numeric($_GET['pg']) ? $_GET['pg'] : 1;
    }

    public static function getWithPagination($pdo, $tableName, $page, $endPoint, $keyWord) {
        // Number of events per page
        $pageLimit = 10;

        // Validate the page
        $query = "SELECT COUNT(*) AS total FROM " . $tableName;
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $total = intval($stmt->fetch()["total"]);
        $numberOfPage = ceil($total / $pageLimit);
        $numberOfPage = $numberOfPage == 0 ? 1 : $numberOfPage;

        // Page not found.
        if ($page > $numberOfPage) {
            $returnData["pageNotFound"] = "Page not found.";
        }
        else {
            $offset = $page == 1 ? 0 : ($pageLimit * ($page - 1));

            // Pagination urls
            $nextUrl = ($page < $numberOfPage) ? $_SERVER["SERVER_NAME"] . $endPoint .'?pg=' . ($page + 1) : null;
            $previousUrl = $page > 1 ? $_SERVER["SERVER_NAME"] . $endPoint .'?pg=' . ($page - 1) : null;

            // Get all event
            $query = "SELECT * FROM " . $tableName . " LIMIT :_limit OFFSET :off_set";
            $options = ['_limit' => $pageLimit, 'off_set' => $offset];
            $stmt = $pdo->prepare($query);
            $stmt->execute($options);

            // Fetching
            $data = $stmt->fetchAll();

            $rowReturned = count($data);
            if ($rowReturned < $pageLimit || isset($_GET['e'])) $nextUrl = null;

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

}