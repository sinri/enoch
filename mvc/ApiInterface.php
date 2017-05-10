<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 23:08
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibRequest;
use sinri\enoch\core\LibResponse;

class ApiInterface
{
    protected $request;
    protected $response;
    protected $request_guid;

    public function __construct()
    {
        $this->request_guid = uniqid();
        $this->request = new LibRequest();
        $this->response = new LibResponse();
    }

    public function _work($defaultMethod = '')
    {
        $this->_beforeWork();

        $method = $this->request->getRequest("method", $defaultMethod, "/^[a-zA-Z0-9]+$/");
        if (empty($method) || !method_exists($this, $method)) {
            throw new BaseCodedException("Method not exists", BaseCodedException::METHOD_NOT_EXISTS);
        }

        try {
            $data = $this->$method();
            $this->_sayOK($data);
        } catch (BaseCodedException $exception) {
            $this->_sayFail([
                "error_code" => $exception->getCode(),
                "error_msg" => $exception->getMessage(),
            ]);
        }
    }

    protected function _beforeWork()
    {
    }

    protected function _sayOK($data = "")
    {
        $this->response->jsonForAjax(LibResponse::AJAX_JSON_CODE_OK, $data);
    }

    protected function _sayFail($error = "")
    {
        $this->response->jsonForAjax(LibResponse::AJAX_JSON_CODE_FAIL, $error);
    }
}