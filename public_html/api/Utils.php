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
        return $_SESSION['admin'] === 1;
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

}