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
    // @since 1.2.8
    const ROUTE_PARAM_MIDDLEWARE = "MIDDLEWARE";

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
     * @param $middleware MiddlewareInterface
     */
    protected function registerRoute($method, $path, $callback, $middleware = null)
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
            self::ROUTE_PARAM_MIDDLEWARE => $middleware,
        ]);
    }

    public function get($path, $callback, $middleware = null)
    {
        $this->registerRoute(Spirit::METHOD_GET, $path, $callback, $middleware);
    }

    public function post($path, $callback, $middleware = null)
    {
        $this->registerRoute(Spirit::METHOD_POST, $path, $callback, $middleware);
    }

    public function put($path, $callback, $middleware = null)
    {
        $this->registerRoute(Spirit::METHOD_PUT, $path, $callback, $middleware);
    }

    public function patch($path, $callback, $middleware = null)
    {
        $this->registerRoute(Spirit::METHOD_PATCH, $path, $callback, $middleware);
    }

    public function delete($path, $callback, $middleware = null)
    {
        $this->registerRoute(Spirit::METHOD_DELETE, $path, $callback, $middleware);
    }

    public function option($path, $callback, $middleware = null)
    {
        $this->registerRoute(Spirit::METHOD_OPTION, $path, $callback, $middleware);
    }

    public function head($path, $callback, $middleware = null)
    {
        $this->registerRoute(Spirit::METHOD_HEAD, $path, $callback, $middleware);
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
                // @since 1.2.8 the shift job moved here
                if (!empty($matches)) array_shift($matches);
                $route[self::ROUTE_PARSED_PARAMETERS] = $matches;
                return $route;
            }
        }
        throw new BaseCodedException("No route matched.", BaseCodedException::NO_MATCHED_ROUTE);
    }

}
