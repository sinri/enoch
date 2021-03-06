<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/28
 * Time: 22:17
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibRequest;
use sinri\enoch\core\LibResponse;

abstract class RouterInterface
{
    protected $request;
    protected $response;
    protected $error_handler = null;
    protected $routes = [];

    protected $default_controller_name = null;
    protected $default_method_name = null;

    /**
     * @return null
     */
    public function getDefaultControllerName()
    {
        return $this->default_controller_name;
    }

    /**
     * @param null $default_controller_name
     */
    public function setDefaultControllerName($default_controller_name)
    {
        $this->default_controller_name = $default_controller_name;
    }

    /**
     * @return null
     */
    public function getDefaultMethodName()
    {
        return $this->default_method_name;
    }

    /**
     * @param null $default_method_name
     */
    public function setDefaultMethodName($default_method_name)
    {
        $this->default_method_name = $default_method_name;
    }


    protected $debug = false;

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function __construct()
    {
        $this->request = new LibRequest();
        $this->response = new LibResponse();
        $this->error_handler = null;
        $this->routes = [];
        $this->debug = false;
    }

    /**
     * @param null $errorHandler
     */
    public function setErrorHandler($errorHandler)
    {
        $this->error_handler = $errorHandler;
    }

    /**
     * @param array $errorData
     * @param int $http_code @since 1.2.8
     */
    public function handleRouteError($errorData = [], $http_code = 404)
    {
        try {
            http_response_code($http_code);
            if (is_string($this->error_handler) && file_exists($this->error_handler)) {
                $this->response->displayPage($this->error_handler, $errorData);
                return;
            } elseif (is_callable($this->error_handler)) {
                call_user_func_array($this->error_handler, [$errorData]);
                return;
            }
            $this->response->errorPage(json_encode($errorData));
        } catch (\Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getTraceAsString();
        }

    }

    abstract public function seekRoute($path, $method);

    /**
     * @since 1.4.2
     * @param $path
     * @param $callback
     * @param null $middleware
     */
    abstract public function any($path, $callback, $middleware = null);

    abstract public function get($path, $callback, $middleware = null);

    abstract public function post($path, $callback, $middleware = null);

    abstract public function put($path, $callback, $middleware = null);

    abstract public function patch($path, $callback, $middleware = null);

    abstract public function delete($path, $callback, $middleware = null);

    abstract public function option($path, $callback, $middleware = null);

    abstract public function head($path, $callback, $middleware = null);

    abstract public function group($shared, $list);

    abstract public function loadController($basePath, $controllerClass, $middleware = null);
}
