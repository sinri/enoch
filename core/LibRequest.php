<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 23:41
 */

namespace sinri\enoch\core;


use sinri\enoch\helper\CommonHelper;

class LibRequest
{
    const METHOD_HEAD = "HEAD";//since v1.3.0
    const METHOD_GET = "GET";//since v1.3.0
    const METHOD_POST = "POST";//since v1.3.0
    const METHOD_PUT = "PUT";//since v1.3.0
    const METHOD_DELETE = "DELETE";//since v1.3.0
    const METHOD_OPTION = "OPTION";//since v1.3.0
    const METHOD_PATCH = "PATCH";//since v1.3.0
    const METHOD_CLI = "cli";//since v1.3.0

    protected $helper;

    public function __construct()
    {
        $this->helper = new CommonHelper();
    }

    public function getRequest($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_REQUEST, $name, $default, $regex, $error);
        return $value;
    }

    public function get($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_GET, $name, $default, $regex, $error);
        return $value;
    }

    public function post($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_POST, $name, $default, $regex, $error);
        return $value;
    }

    public function fullPostFields()
    {
        return $_POST ? $_POST : [];
    }

    public function fullGetFields()
    {
        return $_GET ? $_GET : [];
    }

    /**
     * 是否是AJAx提交的
     */
    public function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        }
        return false;
    }

    /**
     * 是否是GET提交的
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是POST提交
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
    }

    /**
     * @since v1.1.0
     * @return string|bool return request method, or false on failed.
     */
    public function getRequestMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return $_SERVER['REQUEST_METHOD'];
        }
        return $this->isCLI() ? self::METHOD_CLI : false;
    }

    public function isCLI()
    {
        return (php_sapi_name() === 'cli') ? true : false;
    }
}