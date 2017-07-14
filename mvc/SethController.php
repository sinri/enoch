<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 23:08
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibResponse;

/**
 * Designed for Request Based MVC, not specially for router based mvc
 * Class SethController renamed from ApiInterface with implementation of SethInterface @since ^2.0
 * @package sinri\enoch\mvc
 */
class SethController implements SethInterface
{
    protected $request_uuid;

    public function __construct($initData = null)
    {
        $this->request_uuid = uniqid();
    }

    protected function _sayOK($data = "")
    {
        LibResponse::jsonForAjax(LibResponse::AJAX_JSON_CODE_OK, $data);
    }

    protected function _sayFail($error = "")
    {
        LibResponse::jsonForAjax(LibResponse::AJAX_JSON_CODE_FAIL, $error);
    }
}