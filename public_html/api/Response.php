<?php

namespace api;

class Response
{
    private $_success;
    private $_httpStatusCode;
    private $_messages = array();
    private $_data;
//    private $_toCache = false;
    private $_responseData = array();

    /**
     * @param mixed $success
     */
    public function setSuccess($success)
    {
        $this->_success = $success;
    }

    /**
     * @param mixed $httpStatusCode
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->_httpStatusCode = $httpStatusCode;
    }

    /**
     * @param $message
     */
    public function AddMessages($message)
    {
        $this->_messages[] = $message;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

//    /**
//     * @param bool $toCache
//     */
//    public function setToCache($toCache)
//    {
//        $this->_toCache = $toCache;
//    }

    public function send() {
        header('Content-type: application/json;charset=utf-8');
        header('Access-Control-Allow-Origin: *');

//        if ($this->_toCache == true) header('Cache-control: max-age-60');
//        else header('Cache-control: no-cache, no-store');

        if ($this->_success !== false && $this->_success !== true || !is_numeric($this->_httpStatusCode)) {
            http_response_code(500);
            $this->_responseData['statusCode'] = 500;
            $this->_responseData['success'] = false;
            $this->AddMessages("Internal server error");
            $this->_responseData['messages'] = $this->_messages;
        } else {
            http_response_code($this->_httpStatusCode);
            $this->_responseData['statusCode'] = $this->_httpStatusCode;
            $this->_responseData['success'] = $this->_success;
            $this->_responseData['messages'] = $this->_messages;
            $this->_responseData['data'] = $this->_data;
        }
        echo json_encode($this->_responseData);
    }

}