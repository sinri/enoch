<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 22:42
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibSession;
use sinri\enoch\core\Spirit;

class Lamech
{
    protected $gateway = "index.php";
    protected $session_dir;
    protected $controller_dir;
    protected $view_dir;
    protected $error_page;
    protected $spirit;
    protected $router;
    private $default_controller_name = 'Welcome';
    private $default_method_name = 'index';

    public function __construct($sessionDir = null, $controllerDir = null, $viewDir = null, $errorPage = null)
    {
        $this->session_dir = $sessionDir;
        $this->controller_dir = $controllerDir;
        $this->view_dir = $viewDir;
        $this->error_page = $errorPage;

        $this->router = new Naamah();

        $this->spirit = new Spirit();
    }

    /**
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param string $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return Naamah
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return string
     */
    public function getDefaultControllerName()
    {
        return $this->default_controller_name;
    }

    /**
     * @param string $defaultControllerName
     */
    public function setDefaultControllerName($defaultControllerName)
    {
        $this->default_controller_name = $defaultControllerName;
    }

    /**
     * @return string
     */
    public function getDefaultMethodName()
    {
        return $this->default_method_name;
    }

    /**
     * @param string $defaultMethodName
     */
    public function setDefaultMethodName($defaultMethodName)
    {
        $this->default_method_name = $defaultMethodName;
    }

    /**
     * @return null
     */
    public function getErrorPage()
    {
        return $this->error_page;
    }

    /**
     * @param null $errorPage
     */
    public function setErrorPage($errorPage)
    {
        $this->error_page = $errorPage;
    }

    /**
     * @return mixed
     */
    public function getSessionDir()
    {
        return $this->session_dir;
    }

    /**
     * @param mixed $sessionDir
     */
    public function setSessionDir($sessionDir)
    {
        $this->session_dir = $sessionDir;
    }

    /**
     * @return mixed
     */
    public function getControllerDir()
    {
        return $this->controller_dir;
    }

    /**
     * @param mixed $controllerDir
     */
    public function setControllerDir($controllerDir)
    {
        $this->controller_dir = $controllerDir;
    }

    /**
     * @return mixed
     */
    public function getViewDir()
    {
        return $this->view_dir;
    }

    /**
     * @param mixed $viewDir
     */
    public function setViewDir($viewDir)
    {
        $this->view_dir = $viewDir;
    }

    public function startSession()
    {
        // instead of this
        //LibSession::sessionStart($this->session_dir);

        if (!empty($session_dir)) {
            session_save_path($session_dir);
        }
        //指定LibSession为会话处理代理
        $handler = new LibSession();
        session_set_save_handler($handler, true);
        //启动新会话或者重用现有会话
        session_start();
        //获取当前会话 ID
        $session_id = session_id();
        $handler->setSessionID($session_id);
        //读取会话名称
        $session_name = session_name();
        $handler->setSessionName($session_name);
    }

    public function viewFromRequest()
    {
        //$spirit = Spirit::getInstance();
        $act = $this->spirit->getRequest("act", 'index', "/^[A-Za-z0-9_]+$/", $error);
        if ($error === Spirit::REQUEST_REGEX_NOT_MATCH) {
            $this->spirit->errorPage("Act input does not correct.", null, $this->error_page);
            return;
        }

        //act 种类
        try {
            $view_path = $this->view_dir . '/' . $act . ".php";
            if (!file_exists($view_path)) {
                throw new BaseCodedException("Act missing", BaseCodedException::ACT_NOT_EXISTS);
            }
            $this->spirit->displayPage($view_path, []);
        } catch (\Exception $exception) {
            $this->spirit->errorPage("Act met error: " . $exception->getMessage(), $exception, $this->error_page);
        }

    }

    public function apiFromRequest($apiNamespace = "\\")
    {
        //$spirit = Spirit::getInstance();
        $act = $this->spirit->getRequest("act", $this->default_controller_name, "/^[A-Za-z0-9_]+$/", $error);
        if ($error !== Spirit::REQUEST_NO_ERROR) {
            $this->spirit->jsonForAjax(Spirit::AJAX_JSON_CODE_FAIL, "Not correct request " . $error);
            return;
        }
        try {
            $target_class = $apiNamespace . $act;
            $target_class_path = $this->controller_dir . '/' . $act . '.php';
            if (!file_exists($target_class_path)) {
                throw new BaseCodedException("Controller lack.");
            }
            require_once $target_class_path;
            $api = new $target_class();
            //$api->_work($this->default_method_name);
            call_user_func_array([$api, '_work'], [$this->default_method_name]);
        } catch (BaseCodedException $exception) {
            $this->spirit->jsonForAjax(
                Spirit::AJAX_JSON_CODE_FAIL,
                ["error_code" => $exception->getCode(), "error_msg" => "Exception: " . $exception->getMessage()]
            );
        }
    }

    public function restfullyHandleRequest($apiNamespace = "\\")
    {
        //$spirit = Spirit::getInstance();
        //$request_method = $_SERVER['REQUEST_METHOD'];//HEAD,GET,POST,PUT,etc.
        //$query_string = $_SERVER['QUERY_STRING'];//act=ExampleAPI&method=test
        //$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';// /a/b/c

        $act = $this->getController($sub_paths);
        $method = $this->default_method_name;//default method
        if (isset($sub_paths[0]) && $sub_paths[0] !== '') {
            $method = $sub_paths[0];
            unset($sub_paths[0]);
        }

        try {
            $target_class = $apiNamespace . $act;
            $target_class_path = $this->controller_dir . '/' . $act . '.php';
            if (!file_exists($target_class_path)) {
                throw new BaseCodedException("Controller lack: " . $target_class_path);
            }
            require_once $target_class_path;
            $api = new $target_class();
            if (!method_exists($api, $method)) {
                throw new BaseCodedException("Target class has not this method.");
            }
            return call_user_func_array([$api, $method], $sub_paths);
        } catch (BaseCodedException $exception) {
            $this->spirit->jsonForAjax(
                Spirit::AJAX_JSON_CODE_FAIL,
                ["error_code" => $exception->getCode(), "error_msg" => "Exception: " . $exception->getMessage()]
            );
        }
        return false;
    }

    protected function getController(&$subPaths = array())
    {
        //$this->spirit = Spirit::getInstance();
        if ($this->spirit->isCLI()) {
            return $this->getControllerForCLI($subPaths);
        }

        $controller_name = $this->default_controller_name;
        $subPaths = [];
        $controllerIndex = $this->getControllerIndex();
        $pattern = '/^\/([^\?]*)(\?|$)/';
        $r = preg_match($pattern, $controllerIndex, $matches);
        if (!$r) {
            return $controller_name;
        }
        $controller_array = explode('/', $matches[1]);
        if (count($controller_array) > 0) {
            $controller_name = $controller_array[0];
            if (count($controller_array) > 1) {
                unset($controller_array[0]);
                $subPaths = array_filter($controller_array, function ($var) {
                    return $var !== '';
                });
                $subPaths = array_values($subPaths);
            }
        }

        if (empty($controller_name)) {
            $controller_name = $this->default_controller_name;
        }
        return $controller_name;
    }

    protected function getControllerForCLI(&$subPaths = array())
    {
        global $argv;
        global $argc;
        $controller_name = $this->default_controller_name;
        $subPaths = [];
        $subPaths = array();
        for ($i = 1; $i < $argc; $i++) {
            if ($i == 1) {
                $controller_name = $argv[$i];
                continue;
            }
            $subPaths[] = $argv[$i];
        }
        return $controller_name;
    }

    protected function getControllerIndex()
    {
        $prefix = $_SERVER['SCRIPT_NAME'];
        if (
            (strpos($_SERVER['REQUEST_URI'], $prefix) !== 0)
            && (strrpos($prefix, '/' . $this->gateway) + 10 == strlen($prefix))
        ) {
            $prefix = substr($prefix, 0, strlen($prefix) - 10);
        }

        return substr($_SERVER['REQUEST_URI'], strlen($prefix));
    }

    public function handleRequestWithRoutes($apiNamespace = "\\")
    {
        try {
            $parts = $this->dividePath($path_string);
            //$this->spirit->log(Spirit::LOG_INFO,__METHOD__."-part",$parts);
            //$this->spirit->log(Spirit::LOG_INFO,__METHOD__."-path_string",$path_string);
            $route = $this->router->seekRoute($path_string, $this->spirit->getRequestMethod());
            //$this->spirit->log(Spirit::LOG_INFO,__METHOD__."-route",$route);
            if ($route[Naamah::ROUTE_PARAM_TYPE] == Naamah::ROUTE_TYPE_FUNCTION) {
                $callable = $route[Naamah::ROUTE_PARAM_TARGET];
                $this->handleRouteWithFunction($callable, $apiNamespace, $parts);
                return;
            } elseif ($route[Naamah::ROUTE_PARAM_TYPE] == Naamah::ROUTE_TYPE_VIEW) {
                $target = $route[Naamah::ROUTE_PARAM_TARGET];
                $this->handleRouteWithView($target, $parts);
                return;
            }
            throw new BaseCodedException("Naamah Error with unknown type");
        } catch (\Exception $exception) {
            //var_dump($exception);
            $this->router->handleRouteError(
                [
                    "error_code" => $exception->getCode(),
                    "error_message" => $exception->getMessage(),
                ]
            );
        }
    }

    protected function dividePath(&$pathString = '')
    {
        $sub_paths = array();
        //$spirit = Spirit::getInstance();
        if ($this->spirit->isCLI()) {
            global $argv;
            global $argc;
            for ($i = 1; $i < $argc; $i++) {
                $sub_paths[] = $argv[$i];
            }
            return $sub_paths;
        }

        $pathString = $this->getControllerIndex();
        $pattern = '/^\/([^\?]*)(\?|$)/';
        $r = preg_match($pattern, $pathString, $matches);
        if (!$r) {
            // https://github.com/sinri/enoch/issues/1
            // this bug (return '' which is not an array) fixed since v1.0.2
            return [''];
        }
        $controller_array = explode('/', $matches[1]);
        if (count($controller_array) > 0) {
            $sub_paths = array_filter($controller_array, function ($var) {
                return $var !== '';
            });
            $sub_paths = array_values($sub_paths);
        }

        return $sub_paths;
    }

    private function handleRouteWithFunction($callable, $apiNamespace, $parts)
    {
        if (is_array($callable)) {
            $act = $this->default_controller_name;
            $method = $this->default_method_name;
            if (count($callable) > 0) {
                $act = $callable[0];
            }
            if (count($callable) > 1) {
                $method = $callable[1];
            }
            $target_class = $apiNamespace . $act;
            $target_class_path = $this->controller_dir . '/' . $act . '.php';
            if (!file_exists($target_class_path)) {
                throw new BaseCodedException("Controller lack: " . $target_class_path);
            }
            require_once $target_class_path;
            $api = new $target_class();

            return call_user_func_array([$api, $method], [$parts]);
        } elseif (is_callable($callable)) {
            return call_user_func_array($callable, [$parts]);
        }
        throw new BaseCodedException("DIED");
    }

    private function handleRouteWithView($target, $parts)
    {
        //$spirit = Spirit::getInstance();
        $view_path = $this->view_dir . '/' . $target . ".php";
        if (!file_exists($view_path)) {
            throw new BaseCodedException("View missing", BaseCodedException::VIEW_NOT_EXISTS);
        }
        $this->spirit->displayPage($view_path, ["url_path_parts" => $parts]);
    }
}