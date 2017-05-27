<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/28
 * Time: 21:33
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibRequest;

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
    // @since 1.3.2 only use in group
    const ROUTE_PARAM_NAMESPACE = "NAMESPACE";

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
        if ($this->debug) {
            echo __METHOD__ . "(" . json_encode($method) . ", " . json_encode($path) . ", " . json_encode($callback) . ", " . json_encode($middleware) . PHP_EOL;
        }
        $regex = [];
        $param_names = [];

        /*
        // METHOD 1
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
        */

        // METHOD 2
        $path = preg_replace('/\//', '\/', $path);
        $matched = preg_match_all('/\{([^\/]+)\}/', $path, $matches);
        if ($matched) {
            if ($this->debug) {
                print_r($matches);
            }
            $regex = preg_replace('/\{([^\/]+)\}/', '([^\/]+)', $path);
        } else {
            $regex = $path;
        }
        $regex = '/^\/' . $regex . '$/';

        $new_route = [
            self::ROUTE_PARAM_METHOD => $method,
            self::ROUTE_PARAM_PATH => $regex,
            self::ROUTE_PARAM_CALLBACK => $callback,
            self::ROUTE_PARAM_MIDDLEWARE => $middleware,
        ];
        if ($this->debug) {
            print_r($new_route);
        }
        array_unshift($this->routes, $new_route);
    }

    public function get($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_GET, $path, $callback, $middleware);
    }

    public function post($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_POST, $path, $callback, $middleware);
    }

    public function put($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_PUT, $path, $callback, $middleware);
    }

    public function patch($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_PATCH, $path, $callback, $middleware);
    }

    public function delete($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_DELETE, $path, $callback, $middleware);
    }

    public function option($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_OPTION, $path, $callback, $middleware);
    }

    public function head($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_HEAD, $path, $callback, $middleware);
    }

    public function seekRoute($path, $method)
    {
        if ($path == '') $path = '/';
        foreach ($this->routes as $route) {
            $route_regex = $route[self::ROUTE_PARAM_PATH];
            $route_method = $route[self::ROUTE_PARAM_METHOD];
            if ($this->debug) {
                echo __METHOD__ . " TRY TO MATCH RULE: [$route_method][$route_regex][$path]" . PHP_EOL;
            }
            if (!empty($route_method) && stripos($route_method, $method) === false) {
                if ($this->debug) {
                    echo __METHOD__ . " ROUTE METHOD NOT MATCH [$method]" . PHP_EOL;
                }
                continue;
            }
            if (preg_match($route_regex, $path, $matches)) {
                // @since 1.2.8 the shift job moved here
                if (!empty($matches)) array_shift($matches);
                $matches = array_filter($matches, function ($v) {
                    return substr($v, 0, 1) != '/';
                });
                $matches = array_values($matches);
                array_walk($matches, function (&$v, $k) {
                    $v = urldecode($v);
                });
                $route[self::ROUTE_PARSED_PARAMETERS] = $matches;
                if ($this->debug) {
                    echo __METHOD__ . " MATACHED with " . json_encode($matches) . PHP_EOL;
                }
                return $route;
            }
        }
        throw new BaseCodedException(
            "No route matched: path={$path} method={$method}",
            BaseCodedException::NO_MATCHED_ROUTE
        );
    }

    /**
     * @since 1.3.1
     * @param $shared array
     * @param $list array
     * @return void
     */
    public function group($shared, $list)
    {
        $middleware = null;
        $sharedPath = '';
        $sharedNamespace = '';
        if (isset($shared[self::ROUTE_PARAM_MIDDLEWARE])) {
            $middleware = $shared[self::ROUTE_PARAM_MIDDLEWARE];
        }
        if (isset($shared[self::ROUTE_PARAM_PATH])) {
            $sharedPath = $shared[self::ROUTE_PARAM_PATH];
        }
        if (isset($shared[self::ROUTE_PARAM_NAMESPACE])) {
            $sharedNamespace = $shared[self::ROUTE_PARAM_NAMESPACE];
        }

        foreach ($list as $item) {
            $callback = $item[self::ROUTE_PARAM_CALLBACK];
            if (is_array($callback) && isset($callback[0]) && is_string($callback[0])) {
                $callback[0] = $sharedNamespace . $callback[0];
            }
            $this->registerRoute(
                $item[self::ROUTE_PARAM_METHOD],
                $sharedPath . $item[self::ROUTE_PARAM_PATH],
                $callback,
                $middleware
            );
        }
    }

    /**
     * Like CI, bind a controller to a base URL
     * @param string $basePath controller/base/
     * @param string $controllerClass app/controller/controllerA
     * @param null|MiddlewareInterface $middleware as is
     * @throws BaseCodedException
     */
    public function loadController($basePath, $controllerClass, $middleware = null)
    {
        $method_list = get_class_methods($controllerClass);
        $reflector = new \ReflectionClass($controllerClass);
        foreach ($method_list as $method) {
            if (strpos($method, '_') === 0) {
                continue;
            }
            $path = $basePath . $method;
            $parameters = $reflector->getMethod($method)->getParameters();
            $after_string = "";
            $came_in_default_area = false;
            if (!empty($parameters)) {
                foreach ($parameters as $param) {
                    if ($param->isDefaultValueAvailable()) {
                        $path .= "(";
                        $after_string .= ")?";
                        $came_in_default_area = true;
                    } elseif ($came_in_default_area) {
                        //non-default after default
                        throw new BaseCodedException("ROUTE SETTING ERROR: required-parameter after non-required-parameter");
                    }
                    $path .= '/{' . $param->name . '}';
                }
                $path .= $after_string;
            }
            $this->registerRoute(null, $path, [$controllerClass, $method], $middleware);
        }
    }

    /**
     * @since 1.4.2
     * @param $path
     * @param $callback
     * @param null $middleware
     * @return void
     */
    public function any($path, $callback, $middleware = null)
    {
        $this->registerRoute(null, $path, $callback, $middleware);
    }
}
