<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/28
 * Time: 21:33
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\Spirit;

/**
 * Class Adah
 * @package sinri\enoch\mvc
 * @since 1.2.0
 */
class Adah extends RouterInterface
{
    const ROUTE_PARAM_METHOD = "METHOD";
    const ROUTE_PARAM_PATH = "PATH";
    const ROUTE_PARAM_CALLBACK = "CALLBACK";

    const ROUTE_PARSED_PARAMETERS = "PARSED";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Designed after Lumen Routing: https://lumen.laravel-china.org/docs/5.3/routing
     * posts/{post}/comments/{comment}
     * @param $method
     * @param $path
     * @param $callback
     */
    protected function registerRoute($method, $path, $callback)
    {
        $regex = [];
        $param_names = [];
        $list = explode('/', $path);
        if (empty($list)) {
            $list = [];
        }
        foreach ($list as $item) {
            if (preg_match('/^\{([^\/]+)\}$/', $item, $matches) && isset($matches[1])) {
                $param_names[] = $matches[1];
                $regex[] = '([^\/]+)';
                continue;
            }
            $regex[] = $item;
        }
        $regex = implode('\/', $regex);
        $regex = '/^\/' . $regex . '$/';
        array_unshift($this->routes, [
            self::ROUTE_PARAM_METHOD => $method,
            self::ROUTE_PARAM_PATH => $regex,
            self::ROUTE_PARAM_CALLBACK => $callback,
        ]);
    }

    public function get($path, $callback)
    {
        $this->registerRoute(Spirit::METHOD_GET, $path, $callback);
    }

    public function post($path, $callback)
    {
        $this->registerRoute(Spirit::METHOD_POST, $path, $callback);
    }

    public function put($path, $callback)
    {
        $this->registerRoute(Spirit::METHOD_PUT, $path, $callback);
    }

    public function patch($path, $callback)
    {
        $this->registerRoute(Spirit::METHOD_PATCH, $path, $callback);
    }

    public function delete($path, $callback)
    {
        $this->registerRoute(Spirit::METHOD_DELETE, $path, $callback);
    }

    public function option($path, $callback)
    {
        $this->registerRoute(Spirit::METHOD_OPTION, $path, $callback);
    }

    public function head($path, $callback)
    {
        $this->registerRoute(Spirit::METHOD_HEAD, $path, $callback);
    }

    public function seekRoute($path, $method)
    {
        if ($path == '') $path = '/';
        foreach ($this->routes as $route) {
            $route_regex = $route[self::ROUTE_PARAM_PATH];
            $route_method = $route[self::ROUTE_PARAM_METHOD];
            //echo "[$route_method][$route_regex][$path]";
            if (!empty($route_method) && stripos($route_method, $method) === false) {
                //echo "ROUTE METHOD NOT MATCH [$method]".PHP_EOL;
                continue;
            }
            if (preg_match($route_regex, $path, $matches)) {
                $route[self::ROUTE_PARSED_PARAMETERS] = $matches;
                return $route;
            }
        }
        throw new BaseCodedException("No route matched.", BaseCodedException::NO_MATCHED_ROUTE);
    }

}
