<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 11:02
 */

namespace sinri\enoch\core;


use sinri\enoch\helper\CommonHelper;

/**
 * @deprecated since 1.3.0
 * Class Spirit
 * @package sinri\enoch\core
 */
class Spirit
{
    const LOG_INFO = 'INFO';
    const LOG_WARNING = 'WARNING';
    const LOG_ERROR = 'ERROR';

    protected static $instance = null;
    protected static $useColoredTerminalOutput = false;
    protected $logger = null;
    protected $helper = null;
    protected $request = null;
    protected $response = null;

    /**
     * @deprecated since 1.2.9
     * @return bool
     */
    public static function isUseColoredTerminalOutput()
    {
        return self::$useColoredTerminalOutput;
    }

    /**
     * @deprecated since 1.2.9
     * @param bool $useColoredTerminalOutput
     */
    public static function setUseColoredTerminalOutput($useColoredTerminalOutput)
    {
        self::$useColoredTerminalOutput = $useColoredTerminalOutput;
    }

    public function __construct()
    {
        $this->logger = new LibLog();
        $this->helper = new CommonHelper();
        $this->request = new LibRequest();
        $this->response = new LibResponse();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Spirit();
        }
        return self::$instance;
    }

    /**
     * @deprecated use LibLog instead since 1.2.9
     * @param $level
     * @param $message
     * @param string $object
     * @return string
     */
    final public function generateLog($level, $message, $object = '')
    {
        if (self::$useColoredTerminalOutput) {
            $this->logger->setUseColoredTerminalOutput(true);
        }
        return $this->logger->generateLog($level, $message, $object);
    }

    /**
     * This could be overrode for customized log output
     * @deprecated use LibLog instead since 1.2.9
     * @param $level
     * @param $message
     * @param string $object
     */
    public function log($level, $message, $object = '')
    {
        if (self::$useColoredTerminalOutput) {
            $this->logger->setUseColoredTerminalOutput(true);
        }
        $this->logger->generateLog($level, $message, $object);
    }

    const REQUEST_NO_ERROR = 0;
    const REQUEST_FIELD_NOT_FOUND = 1;
    const REQUEST_REGEX_NOT_MATCH = 2;

    const METHOD_HEAD = "HEAD";//since v1.1.0
    const METHOD_GET = "GET";//since v1.1.0
    const METHOD_POST = "POST";//since v1.1.0
    const METHOD_PUT = "PUT";//since v1.1.0
    const METHOD_DELETE = "DELETE";//since v1.1.0
    const METHOD_OPTION = "OPTION";//since v1.1.0
    const METHOD_PATCH = "PATCH";//since v1.2.0
    const METHOD_CLI = "cli";//since v1.1.0

    /**
     * @deprecated since 1.3.0 use CommonHelper instead
     * @param $target
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return null
     */
    public function safeReadArray($target, $name, $default = null, $regex = null, &$error = 0)
    {
        return $this->helper->safeReadArray($target, $name, $default, $regex, $error);
    }

    public function getRequest($name, $default = null, $regex = null, &$error = 0)
    {
        return $this->request->getRequest($name, $default, $regex, $error);
    }

    public function readGet($name, $default = null, $regex = null, &$error = 0)
    {
        return $this->request->get($name, $default, $regex, $error);
    }

    public function readPost($name, $default = null, $regex = null, &$error = 0)
    {
        return $this->request->post($name, $default, $regex, $error);
    }

    public function fullPostFields()
    {
        return $this->request->fullPostFields();
    }

    public function fullGetFields()
    {
        return $this->request->fullGetFields();
    }

    /**
     * 是否是AJAx提交的
     */
    public function isAjax()
    {
        return $this->request->isAjax();
    }

    /**
     * 是否是GET提交的
     */
    public function isGet()
    {
        return $this->request->isGet();
    }

    /**
     * 是否是POST提交
     */
    public function isPost()
    {
        return $this->request->isPost();
    }

    /**
     * @since v1.1.0
     * @return string|bool return request method, or false on failed.
     */
    public function getRequestMethod()
    {
        return $this->request->getRequestMethod();
    }

    public function isCLI()
    {
        return $this->request->isCLI();
    }

    const AJAX_JSON_CODE_OK = "OK";
    const AJAX_JSON_CODE_FAIL = "FAIL";

    public function json($anything)
    {
        $this->response->json($anything);
    }

    /**
     * @param string $code OK or FAIL
     * @param mixed $data
     */
    public function jsonForAjax($code = self::AJAX_JSON_CODE_OK, $data = '')
    {
        $this->response->jsonForAjax($code, $data);
    }

    public function displayPage($filepath, $params = [])
    {
        $this->response->displayPage($filepath, $params);
    }

    public function errorPage($message = '', $exception = null, $viewPath = null)
    {
        $this->response->errorPage($message, $exception, $viewPath);
    }
}
