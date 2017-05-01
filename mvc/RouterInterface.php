<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/28
 * Time: 22:17
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\Spirit;

abstract class RouterInterface
{
    protected $spirit = null;
    protected $error_handler = null;
    protected $routes = [];

    public function __construct()
    {
        $this->spirit = new Spirit();
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

    public function handleRouteError($errorData = [])
    {
        if (is_string($this->error_handler) && file_exists($this->error_handler)) {
            $this->spirit->displayPage($this->error_handler, $errorData);
            return;
        } elseif (is_callable($this->error_handler)) {
            call_user_func_array($this->error_handler, [$errorData]);
            return;
        }
        $this->spirit->errorPage(__METHOD__);
    }

    abstract public function seekRoute($path, $method);

    abstract public function get($path, $callback);

    abstract public function post($path, $callback);

    abstract public function put($path, $callback);

    abstract public function patch($path, $callback);

    abstract public function delete($path, $callback);

    abstract public function option($path, $callback);

    abstract public function head($path, $callback);
}
