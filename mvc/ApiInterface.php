<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 23:08
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\Spirit;

class ApiInterface
{
    protected $spirit;
    protected $request_guid;

    public function __construct()
    {
        $this->request_guid = uniqid();
        $this->spirit = Spirit::getInstance();
    }

    /**
     * @return null|Spirit
     */
    public function getSpirit()
    {
        return $this->spirit;
    }

    public function work()
    {
        $this->beforeWork();

        $method = $this->spirit->getRequest("method", "", "/^[a-zA-Z0-9]+$/", $error);
        if (empty($method) || method_exists($this, $method)) {
            try {
                $data = $this->$method();
                $this->sayOK($data);
            } catch (BaseCodedException $exception) {
                $this->sayFail([
                    "error_code" => $exception->getCode(),
                    "error_msg" => $exception->getMessage(),
                ]);
            }
        } else {
            throw new BaseCodedException("Method not exists", BaseCodedException::METHOD_NOT_EXISTS);
        }
    }

    protected function beforeWork()
    {
    }

    public function sayOK($data = "")
    {
        $this->spirit->jsonForAjax(Spirit::AJAX_JSON_CODE_OK, $data);
    }

    public function sayFail($error = "")
    {
        $this->spirit->jsonForAjax(Spirit::AJAX_JSON_CODE_FAIL, $error);
    }
}