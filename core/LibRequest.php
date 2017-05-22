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

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getRequest($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_REQUEST, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function get($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_GET, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function post($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_POST, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @since 1.3.8
     * @return bool|string
     */
    public function getRequestContent()
    {
        return file_get_contents('php://input');
    }

    /**
     * @since 1.3.8
     * @param bool $assoc
     * @return mixed
     */
    public function getRequestContentAsJson($assoc = true)
    {
        $text = $this->getRequestContent();
        return @json_decode($text, $assoc);
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getCookie($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_COOKIE, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getServerVar($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_SERVER, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getSessionVar($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_SESSION, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getHeader($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($this->fullHeaderFields(), $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @return array
     */
    public function fullPostFields()
    {
        return $_POST ? $_POST : [];
    }

    /**
     * @return array
     */
    public function fullGetFields()
    {
        return $_GET ? $_GET : [];
    }

    /**
     * @return array
     */
    public function fullCookieFields()
    {
        return $_COOKIE ? $_COOKIE : [];
    }

    /**
     * @return array|false|string
     */
    public function fullHeaderFields()
    {
        return getallheaders();
    }

    /**
     * 是否是AJAx提交的
     * @return bool
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
     * @return bool
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是POST提交
     * @return bool
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

    /**
     * @return bool
     */
    public function isCLI()
    {
        return (php_sapi_name() === 'cli') ? true : false;
    }
}