<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/24
 * Time: 13:56
 */

namespace sinri\enoch\mvc;

use sinri\enoch\core\Spirit;

/**
 * Class Naamah
 * Lamech's daughter with Zillah
 * REGEX FOR PATH would not add ^$ to two sides.
 * Route Manager
 * @package sinri\enoch\mvc
 */
class Naamah
{
    const ROUTE_PARAM_TYPE = "type";
    const ROUTE_PARAM_REGEX = "regex";
    const ROUTE_PARAM_TARGET = "target";
    const ROUTE_PARAM_METHOD = "method";//since v1.1.0

    //const ROUTE_TYPE_CLASS='class';
    const ROUTE_TYPE_FUNCTION = 'function';
    const ROUTE_TYPE_VIEW = 'view';

    private $error_handler = null;

    protected $spirit = null;

    /**
     * @param null $errorHandler
     */
    public function setErrorHandler($errorHandler)
    {
        $this->error_handler = $errorHandler;
    }

    protected $routes = [];

    public function __construct()
    {
        $this->routes = [];
        $this->error_handler = null;
        $this->spirit = new Spirit();
    }

    /**
     * @param $regex
     * @param $function
     * @param string $method since v1.1.0
     */
    public function addRouteForFunction($regex, $function, $method = "")
    {
        $route = [
            self::ROUTE_PARAM_TYPE => self::ROUTE_TYPE_FUNCTION,
            self::ROUTE_PARAM_REGEX => $regex,
            self::ROUTE_PARAM_TARGET => $function,
            self::ROUTE_PARAM_METHOD => $method,
        ];
        array_unshift($this->routes, $route);
    }

    /**
     * @param $regex
     * @param $viewName
     * @param string $method since v1.1.0
     */
    public function addRouteForView($regex, $viewName, $method = "")
    {
        $route = [
            self::ROUTE_PARAM_TYPE => self::ROUTE_TYPE_VIEW,
            self::ROUTE_PARAM_REGEX => $regex,
            self::ROUTE_PARAM_TARGET => $viewName,
            self::ROUTE_PARAM_METHOD => $method,
        ];
        array_unshift($this->routes, $route);
    }

    /**
     * @param $path
     * @param string $method since v1.1.0
     * @return mixed
     * @throws BaseCodedException
     */
    public function seekRoute($path, $method = "")
    {
        if ($path == '') $path = '/';
        foreach ($this->routes as $route) {
            $route_regex = $route[self::ROUTE_PARAM_REGEX];
            $route_method = $route[self::ROUTE_PARAM_METHOD];
            //echo "[$route_method][$route_regex][$path]";
            if (!empty($route_method) && stripos($route_method, $method) === false) {
                //echo "ROUTE METHOD NOT MATCH [$method]".PHP_EOL;
                continue;
            }
            if (substr($route_regex, 0, 1) === '/' && substr($route_regex, -1, 1) === '/') {
                if (preg_match($route_regex, $path)) {
                    return $route;
                }
            } else {
                if ("/" . $route_regex == $path) {
                    return $route;
                }
            }
            //echo "REGEX NOT MATCH".PHP_EOL;
        }
        throw new BaseCodedException("No route matched.", BaseCodedException::NO_MATCHED_ROUTE);
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
}