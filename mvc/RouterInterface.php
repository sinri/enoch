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

    public function __construct()
    {
        $this->request = new LibRequest();
        $this->response = new LibResponse();
        $this->error_handler = null;
        $this->routes = [];
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
    public function handleRouteError($errorData = [], $http_code = 200)
    {
        if ($http_code == 403) {
            header('HTTP/1.0 403 Forbidden');
        }
        if (is_string($this->error_handler) && file_exists($this->error_handler)) {
            $this->response->displayPage($this->error_handler, $errorData);
            return;
        } elseif (is_callable($this->error_handler)) {
            call_user_func_array($this->error_handler, [$errorData]);
            return;
        }
        $this->response->errorPage(__METHOD__);
    }

    abstract public function seekRoute($path, $method);

    abstract public function get($path, $callback, $middleware = null);

    abstract public function post($path, $callback, $middleware = null);

    abstract public function put($path, $callback, $middleware = null);

    abstract public function patch($path, $callback, $middleware = null);

    abstract public function delete($path, $callback, $middleware = null);

    abstract public function option($path, $callback, $middleware = null);

    abstract public function head($path, $callback, $middleware = null);
}
