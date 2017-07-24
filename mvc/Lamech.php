<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 22:42
 */

namespace sinri\enoch\mvc;

use sinri\enoch\core\LibLog;
use sinri\enoch\core\LibRequest;
use sinri\enoch\core\LibSession;
use sinri\enoch\helper\CommonHelper;

class Lamech
{
    protected $gateway = "index.php";
    protected $session_dir;
    protected $router;
    protected $debug = false;
    //private $default_controller_name = 'Welcome';
    //private $default_method_name = 'index';

    //protected static $routerType=Adah::ROUTER_TYPE_REGEX;

    public function __construct($sessionDir = null)
    {
        $this->session_dir = $sessionDir;
        $this->router = new Adah(Adah::ROUTER_TYPE_REGEX);
        $this->debug = false;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
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
     * @return Adah
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return string
     * @deprecated moved to Adah
     */
    public function getDefaultControllerName()
    {
        return $this->router->getDefaultControllerName();
    }

    /**
     * @param string $defaultControllerName
     * @deprecated moved to Adah
     */
    public function setDefaultControllerName($defaultControllerName)
    {
        $this->router->setDefaultControllerName($defaultControllerName);
    }

    /**
     * @return string
     * @deprecated moved to Adah
     */
    public function getDefaultMethodName()
    {
        return $this->router->getDefaultMethodName();
    }

    /**
     * @param string $defaultMethodName
     * @deprecated moved to Adah
     */
    public function setDefaultMethodName($defaultMethodName)
    {
        $this->router->setDefaultMethodName($defaultMethodName);
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

    public function startSession()
    {
        // instead of this
        // LibSession::sessionStart($this->session_dir);
        // as it is simple and lazy to manage session inside MVC framework

        if (!empty($this->session_dir)) {
            session_save_path($this->session_dir);
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

    protected function getController(&$subPaths = array())
    {
        if (LibRequest::isCLI()) {
            return $this->getControllerForCLI($subPaths);
        }

        $controller_name = $this->router->getDefaultControllerName();
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
            $controller_name = $this->router->getDefaultControllerName();
        }
        return $controller_name;
    }

    protected function getControllerForCLI(&$subPaths = array())
    {
        global $argv;
        global $argc;
        $controller_name = $this->router->getDefaultControllerName();
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

    protected function dividePath(&$pathString = '')
    {
        $sub_paths = array();
        if (LibRequest::isCLI()) {
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
     * @since 2.1.0 CLI handler support added along with WEB handler.
     */
    public function handleRequest()
    {
        if (LibRequest::isCLI()) {
            $this->handleRequestForCLI();
            return;
        }
        $this->handleRequestForWeb();
    }

    /**
     * @since 2.1.0 CLI handler here.
     */
    public function handleRequestForCLI()
    {
        global $argc;
        global $argv;
        $logger = (new LibLog());
        try {
            // php index.php [PATH] [ARGV]
            $path = CommonHelper::safeReadArray($argv, 1, null);
            if ($path === null) {
                $logger->log(LibLog::LOG_ERROR, "PATH EMPTY", $path);
                return;
            }
            $arguments = [];
            for ($i = 2; $i < $argc; $i++) {
                $arguments[] = $argv[$i];
            }
            $route = $this->router->seekRoute($path, LibRequest::getRequestMethod());
            $callable = CommonHelper::safeReadArray($route, Adah::ROUTE_PARAM_CALLBACK);
            $middleware_chain = CommonHelper::safeReadArray($route, Adah::ROUTE_PARAM_MIDDLEWARE);

            if (!is_array($middleware_chain)) {
                $middleware_chain = [$middleware_chain];
            }
            $preparedData = null;
            foreach ($middleware_chain as $middleware) {
                $middleware_instance = MiddlewareInterface::MiddlewareFactory($middleware);
                $mw_passed = $middleware_instance->shouldAcceptRequest($path, LibRequest::getRequestMethod(), $arguments, $preparedData);
                if (!$mw_passed) {
                    //header('HTTP/1.0 403 Forbidden');
                    throw new BaseCodedException(
                        "Rejected by Middleware " . $middleware,
                        BaseCodedException::REQUEST_FILTER_REJECT
                    );
                }
            }

            if (is_array($callable) && isset($callable[0])) {
                $class_instance = $callable[0];
                $reflectionOfClassName = new \ReflectionClass($class_instance);
                if (in_array('sinri\enoch\mvc\SethInterface', $reflectionOfClassName->getInterfaceNames())) {
                    $class_instance = new $class_instance($preparedData);
                } else {
                    // this branch is for free-style controller... as a backdoor.
                    $class_instance = new $class_instance();
                    if (method_exists($class_instance, '_acceptMiddlewarePreparedData')) {
                        $class_instance->_acceptMiddlewarePreparedData($preparedData);
                    }
                }
                $callable[0] = $class_instance;
            }
            call_user_func_array($callable, $arguments);
        } catch (\Exception $exception) {
            $logger->log(LibLog::LOG_ERROR, "Exception in " . __METHOD__ . " : " . $exception->getMessage());
        }
    }

    /**
     * @since 2.1.0 WEB handler became independent
     */
    public function handleRequestForWeb()
    {
        try {
            $this->dividePath($path_string);
            $route = $this->router->seekRoute($path_string, LibRequest::getRequestMethod());
            $callable = CommonHelper::safeReadArray($route, Adah::ROUTE_PARAM_CALLBACK);
            $params = CommonHelper::safeReadArray($route, Adah::ROUTE_PARSED_PARAMETERS);
            // @since 1.2.8 as MiddlewareInterface
            $middleware_chain = CommonHelper::safeReadArray($route, Adah::ROUTE_PARAM_MIDDLEWARE);

            if ($this->debug) {
//                var_dump([$route, $callable, $params, $middleware_chain]);
            }

            // @since 1.2.8 the shift job moved to Adah
            //if (!empty($params)) array_shift($params);

            // @since 1.5.0 the middleware support chain-style
            if (!is_array($middleware_chain)) {
                $middleware_chain = [$middleware_chain];
            }
            $preparedData = null;
            foreach ($middleware_chain as $middleware) {
                $middleware_instance = MiddlewareInterface::MiddlewareFactory($middleware);
                $mw_passed = $middleware_instance->shouldAcceptRequest($path_string, LibRequest::getRequestMethod(), $params, $preparedData);
                if (!$mw_passed) {
                    //header('HTTP/1.0 403 Forbidden');
                    throw new BaseCodedException(
                        "Rejected by Middleware " . $middleware,
                        BaseCodedException::REQUEST_FILTER_REJECT
                    );
                }
            }

            if (is_array($callable) && isset($callable[0])) {
                $class_instance = $callable[0];
                //print_r(get_class_methods($class_instance));
                // SethInterface Available @since 1.5.0
                $reflectionOfClassName = new \ReflectionClass($class_instance);
                if (in_array('sinri\enoch\mvc\SethInterface', $reflectionOfClassName->getInterfaceNames())) {
                    $class_instance = new $class_instance($preparedData);
                } else {
                    // this branch is for free-style controller... as a backdoor.
                    $class_instance = new $class_instance();
                    if (method_exists($class_instance, '_acceptMiddlewarePreparedData')) {
                        $class_instance->_acceptMiddlewarePreparedData($preparedData);
                    }
                }
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
            if ($this->debug) {
                echo "<pre>" . PHP_EOL;
                print_r($exception);
                echo "</pre>" . PHP_EOL;
            }
        }
    }

    /**
     * Automatically load controllers as Adah Router for Lamech (CI-Style)
     * @since 1.3.6
     * @param string $directory __DIR__ . '/../controller'
     * @param string $urlBase "XX/"
     * @param string $controllerNamespaceBase '\leqee\yiranoc\controller\\'
     * @param string $middleware '\leqee\yiranoc\middleware\AuthMiddleware'
     */
    public function loadAllControllersInDirectoryAsCI($directory, $urlBase = '', $controllerNamespaceBase = '', $middleware = '')
    {
        $this->router->loadAllControllersInDirectoryAsCI($directory, $urlBase, $controllerNamespaceBase, $middleware);
        /*
        // Implementation moved to Adah
        if ($handle = opendir($directory)) {
            if (
                $this->default_controller_name
                && file_exists($directory . '/' . $this->default_controller_name . '.php')
                && $this->default_method_name
                && method_exists($controllerNamespaceBase . $this->default_controller_name, $this->default_method_name)
            ) {
                if($this->routerType===Adah::ROUTER_TYPE_TREE){
                    $urlBaseX=$urlBase;
                    if(strlen($urlBaseX)>0){
                        $urlBaseX=substr($urlBaseX,0,strlen($urlBaseX)-1);
                    }
                    $this->getRouter()->any(
                        $urlBaseX,
                        [$controllerNamespaceBase . $this->default_controller_name, $this->default_method_name],
                        $middleware
                    );
                }else {
                    $this->getRouter()->any(
                        $urlBase . '?',
                        [$controllerNamespaceBase . $this->default_controller_name, $this->default_method_name],
                        $middleware
                    );
                }
            }
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if (is_dir($directory . '/' . $entry)) {
                        //DIR,
                        $this->loadAllControllersInDirectoryAsCI(
                            $urlBase . $entry . '/',
                            $controllerNamespaceBase . $entry . '\\',
                            $middleware
                        );
                    } else {
                        //FILE
                        $list = explode('.', $entry);
                        $name = isset($list[0]) ? $list[0] : '';
                        //$ppp=method_exists($controllerNamespaceBase . $name,$this->default_method_name);
                        //echo "ppp=".json_encode($ppp).PHP_EOL;
                        if (
                            $this->default_method_name
                            && method_exists($controllerNamespaceBase . $name, $this->default_method_name)
                        ) {
                            if($this->routerType===Adah::ROUTER_TYPE_TREE) {
                                $urlBaseX=$urlBase.$name;
                                $this->getRouter()->any(
                                    $urlBaseX,
                                    [$controllerNamespaceBase . $name, $this->default_method_name],
                                    $middleware
                                );
                            }else {
                                $this->getRouter()->any(
                                    $urlBase . $name . '/?',
                                    [$controllerNamespaceBase . $name, $this->default_method_name],
                                    $middleware
                                );
                            }
                        }
                        $this->getRouter()->loadController(
                            $urlBase . $name . '/',
                            $controllerNamespaceBase . $name,
                            $middleware
                        );
                    }
                }
            }
            closedir($handle);
        }
        */
    }
}