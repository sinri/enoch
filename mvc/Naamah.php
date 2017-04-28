<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/24
 * Time: 13:56
 */

namespace sinri\enoch\mvc;

/**
 * Class Naamah
 * Lamech's daughter with Zillah
 * REGEX FOR PATH would not add ^$ to two sides.
 * Route Manager
 * @deprecated sinri v1.2.0
 * @package sinri\enoch\mvc
 */
class Naamah extends RouterInterface
{
    const ROUTE_PARAM_TYPE = "type";
    const ROUTE_PARAM_REGEX = "regex";
    const ROUTE_PARAM_TARGET = "target";
    const ROUTE_PARAM_METHOD = "method";//since v1.1.0

    //const ROUTE_TYPE_CLASS='class';
    const ROUTE_TYPE_FUNCTION = 'function';
    const ROUTE_TYPE_VIEW = 'view';

    public function __construct()
    {
        parent::__construct();
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


}