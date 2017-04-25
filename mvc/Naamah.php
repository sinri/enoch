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

    //const ROUTE_TYPE_CLASS='class';
    const ROUTE_TYPE_FUNCTION = 'function';
    const ROUTE_TYPE_VIEW = 'view';

    private $error_handler = null;

    /**
     * @param null $error_handler
     */
    public function setErrorHandler($error_handler)
    {
        $this->error_handler = $error_handler;
    }

    protected $routes = [];

    public function __construct()
    {
        $this->routes = [];
        $this->error_handler = null;
    }

//    public function addRouteForClass($regex,$class_name){
//        $route=[
//            self::ROUTE_PARAM_TYPE=>self::ROUTE_TYPE_CLASS,
//            self::ROUTE_PARAM_REGEX=>$regex,
//            self::ROUTE_PARAM_TARGET=>$class_name,
//        ];
//        array_unshift($this->routes,$route);
//    }
    public function addRouteForFunction($regex, $function)
    {
        $route = [
            self::ROUTE_PARAM_TYPE => self::ROUTE_TYPE_FUNCTION,
            self::ROUTE_PARAM_REGEX => $regex,
            self::ROUTE_PARAM_TARGET => $function,
        ];
        array_unshift($this->routes, $route);
    }

    public function addRouteForView($regex, $view_name)
    {
        $route = [
            self::ROUTE_PARAM_TYPE => self::ROUTE_TYPE_VIEW,
            self::ROUTE_PARAM_REGEX => $regex,
            self::ROUTE_PARAM_TARGET => $view_name,
        ];
        array_unshift($this->routes, $route);
    }

    public function seekRoute($path)
    {
        if ($path == '') $path = '/';
        foreach ($this->routes as $route) {
            $regex = $route[self::ROUTE_PARAM_REGEX];
            if (preg_match($regex, $path)) {
                return $route;
            }
        }
        throw new BaseCodedException("No route matched.", BaseCodedException::NO_MATCHED_ROUTE);
    }

    public function handleRouteError($error_data = [])
    {
        if (is_string($this->error_handler) && file_exists($this->error_handler)) {
            Spirit::getInstance()->displayPage($this->error_handler, $error_data);
        } elseif (is_callable($this->error_handler)) {
            call_user_func_array($this->error_handler, [$error_data]);
        } else {
            Spirit::getInstance()->errorPage(__METHOD__);
        }
    }
}