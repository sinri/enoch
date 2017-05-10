<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 22:42
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibRequest;
use sinri\enoch\core\LibResponse;
use sinri\enoch\core\LibSession;
use sinri\enoch\helper\CommonHelper;

class Lamech
{
    protected $gateway = "index.php";
    protected $session_dir;
    protected $controller_dir;
    protected $view_dir;
    protected $error_page;
    protected $request;
    protected $response;
    protected $router;
    protected $helper;
    private $default_controller_name = 'Welcome';
    private $default_method_name = 'index';

    public function __construct($sessionDir = null, $controllerDir = null, $viewDir = null, $errorPage = null)
    {
        $this->session_dir = $sessionDir;
        $this->controller_dir = $controllerDir;
        $this->view_dir = $viewDir;
        $this->error_page = $errorPage;
        $this->request = new LibRequest();
        $this->response = new LibResponse();
        $this->router = new Adah();
        $this->helper = new CommonHelper();
    }

    public function useAdahAsRouter()
    {
        $this->router = new Adah();
    }

    /**
     * @deprecated sinri v1.2.0
     */
    public function useNaamahAsRouter()
    {
        $this->router = new Naamah();
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
     * @return RouterInterface
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

    /**
     * @deprecated since v1.2.0
     * alias of handleRequestAsView
     */
    public function viewFromRequest()
    {
        $this->handleRequestAsView();
    }

    public function handleRequestAsView()
    {
        $act = $this->request->getRequest("act", 'index', "/^[A-Za-z0-9_]+$/", $error);
        if ($error === CommonHelper::REQUEST_REGEX_NOT_MATCH) {
            $this->response->errorPage("Act input does not correct.", null, $this->error_page);
            return;
        }

        //act 种类
        try {
            $view_path = $this->view_dir . '/' . $act . ".php";
            if (!file_exists($view_path)) {
                throw new BaseCodedException("Act missing", BaseCodedException::ACT_NOT_EXISTS);
            }
            $this->response->displayPage($view_path, []);
        } catch (\Exception $exception) {
            $this->response->errorPage("Act met error: " . $exception->getMessage(), $exception, $this->error_page);
        }

    }

    /**
     * @deprecated since v1.2.0
     * alias of handleRequestAsApi
     * @param string $apiNamespace
     */
    public function apiFromRequest($apiNamespace = "\\")
    {
        $this->handleRequestAsApi($apiNamespace);
    }

    public function handleRequestAsApi($apiNamespace = "\\")
    {
        $act = $this->request->getRequest("act", $this->default_controller_name, "/^[A-Za-z0-9_]+$/", $error);
        if ($error !== CommonHelper::REQUEST_NO_ERROR) {
            $this->response->jsonForAjax(LibResponse::AJAX_JSON_CODE_FAIL, "Not correct request " . $error);
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
            $this->response->jsonForAjax(
                LibResponse::AJAX_JSON_CODE_FAIL,
                ["error_code" => $exception->getCode(), "error_msg" => "Exception: " . $exception->getMessage()]
            );
        }
    }

    /**
     * @deprecated since v1.2.0
     * alias of handleRequestAsCI
     * @param string $apiNamespace
     * @return bool|mixed
     */
    public function restfullyHandleRequest($apiNamespace = "\\")
    {
        return $this->handleRequestAsCI($apiNamespace);
    }

    public function handleRequestAsCI($apiNamespace = "\\")
    {
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
            $this->response->jsonForAjax(
                LibResponse::AJAX_JSON_CODE_FAIL,
                ["error_code" => $exception->getCode(), "error_msg" => "Exception: " . $exception->getMessage()]
            );
        }
        return false;
    }

    protected function getController(&$subPaths = array())
    {
        if ($this->request->isCLI()) {
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

    /**
     * @deprecated since v1.2.0
     * alias of handleRequestWithNaamah
     * @param string $apiNamespace
     */
    public function handleRequestWithRoutes($apiNamespace = "\\")
    {
        $this->handleRequestWithNaamah($apiNamespace);
    }

    /**
     * @deprecated since v1.2.0
     * @param string $apiNamespace
     */
    public function handleRequestWithNaamah($apiNamespace = "\\")
    {
        try {
            $parts = $this->dividePath($path_string);
            $route = $this->router->seekRoute($path_string, $this->request->getRequestMethod());
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
        if ($this->request->isCLI()) {
            global $argv;
            global $argc;
            for ($i = 1; $i < $argc; $i++) {
                $sub_paths[] = $argv[$i];
            }
            return $sub_paths;
        }

        $fullPathString = $this->getControllerIndex();
        $tmp = explode('?', $fullPathString);
        $pathString = isset($tmp[0]) ? $tmp[0] : '';
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

    /**
     * @deprecated sinri v1.2.0
     * @param $callable
     * @param $apiNamespace
     * @param $parts
     * @return mixed
     * @throws BaseCodedException
     */
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

    /**
     * @deprecated since v1.2.0
     * @param $target
     * @param $parts
     * @throws BaseCodedException
     */
    private function handleRouteWithView($target, $parts)
    {
        $view_path = $this->view_dir . '/' . $target . ".php";
        if (!file_exists($view_path)) {
            throw new BaseCodedException("View missing", BaseCodedException::VIEW_NOT_EXISTS);
        }
        $this->response->displayPage($view_path, ["url_path_parts" => $parts]);
    }

    public function handleRequestThroughAdah()
    {
        try {
            $this->dividePath($path_string);
            $route = $this->router->seekRoute($path_string, $this->request->getRequestMethod());
            $callable = $this->helper->safeReadArray($route, Adah::ROUTE_PARAM_CALLBACK);
            $params = $this->helper->safeReadArray($route, Adah::ROUTE_PARSED_PARAMETERS);
            // @since 1.2.8 as MiddlewareInterface
            $middleware = $this->helper->safeReadArray($route, Adah::ROUTE_PARAM_MIDDLEWARE);

            // @since 1.2.8 the shift job moved to Adah
            //if (!empty($params)) array_shift($params);

            $middleware_instance = MiddlewareInterface::MiddlewareFactory($middleware);
            if (!$middleware_instance->shouldAcceptRequest($path_string, $this->request->getRequestMethod(), $params)) {
                //header('HTTP/1.0 403 Forbidden');
                throw new BaseCodedException(
                    "Rejected by Middleware " . $middleware,
                    BaseCodedException::REQUEST_FILTER_REJECT
                );
            }

            if (is_array($callable) && isset($callable[0])) {
                $class_instance = $callable[0];
                //print_r(get_class_methods($class_instance));
                $class_instance = new $class_instance();
                $callable[0] = $class_instance;
            }
            call_user_func_array($callable, $params);
        } catch (\Exception $exception) {
            $http_code = 200;
            if ($exception->getCode() == BaseCodedException::REQUEST_FILTER_REJECT) {
                $http_code = 403;
            }
            $this->router->handleRouteError(
                [
                    "error_code" => $exception->getCode(),
                    "error_message" => $exception->getMessage(),
                ],
                $http_code
            );
        }
    }
}