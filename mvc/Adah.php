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
    const ROUTE_PARAM_MIDDLEWARE = "MIDDLEWARE";
    const ROUTE_PARAM_NAMESPACE = "NAMESPACE";// only used in `group`

    const ROUTE_PARSED_PARAMETERS = "PARSED";// only used in sought result

//    protected $routerType;
//    const ROUTER_TYPE_TREE = 'TREE';
//    const ROUTER_TYPE_REGEX = 'REGEX';

    /**
     * Adah constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->default_controller_name = 'Welcome';
        $this->default_method_name = 'index';
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
        if ($this->debug) echo __METHOD__ . " : " . json_encode([$method, $path, $callback, $middleware]) . PHP_EOL;
        $path = preg_replace('/\//', '\/', $path);
        $matched = preg_match_all('/\{([^\/]+)\}/', $path, $matches);
        if ($this->debug) echo "Regex Route Variable Components Matched: " . json_encode($matches) . PHP_EOL;
        if ($matched) {
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
        if ($this->debug) echo "New Regex Route: " . json_encode($new_route) . PHP_EOL;
        array_unshift($this->routes, $new_route);

        if (strpos($path, 'Controller') !== false) {
            $path = preg_replace('/Controller/', '', $path);
            $this->registerRoute($method, strtolower($path), $callback, $middleware);
        }
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

    /**
     * @param $path
     * @param $method
     * @return mixed
     * @throws BaseCodedException
     */
    public function seekRoute($path, $method)
    {
        // a possible fix in 2.1.4
        if (strlen($path) > 1 && substr($path, strlen($path) - 1, 1) == '/') {
            $path = substr($path, 0, strlen($path) - 1);
        } elseif ($path == '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            $route_regex = $route[self::ROUTE_PARAM_PATH];
            $route_method = $route[self::ROUTE_PARAM_METHOD];
            if ($this->debug) echo __METHOD__ . " TRY TO MATCH RULE: [$route_method][$route_regex][$path]" . PHP_EOL;
            if (!empty($route_method) && stripos($route_method, $method) === false) {
                if ($this->debug) echo __METHOD__ . " ROUTE METHOD NOT MATCH [$method]" . PHP_EOL;
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
                if ($this->debug) echo __METHOD__ . " MATCHED with " . json_encode($matches) . PHP_EOL;
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
     * @throws \ReflectionException
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
                //self::ROUTER_TYPE_REGEX
                foreach ($parameters as $param) {
                    if ($param->isDefaultValueAvailable()) {
                        $path .= "(";
                        $after_string .= ")?";
                        $came_in_default_area = true;
                    } elseif ($came_in_default_area) {
                        //non-default after default
                        if ($this->debug) {
                            echo ("ROUTE SETTING ERROR: required-parameter after non-required-parameter") . PHP_EOL;
                        }
                        return;
                    }
                    $path .= '/{' . $param->name . '}';
                }
                $path .= $after_string;
            }
            $this->registerRoute(null, $path, [$controllerClass, $method], $middleware);
            if ($method == $this->default_method_name) {
                $basePathX = $basePath;
                if (strlen($basePathX) > 0) {
                    $basePathX = substr($basePathX, 0, strlen($basePathX) - 1);
                }
                $this->registerRoute(null, $basePathX, [$controllerClass, $method], $middleware);
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
     * @throws \ReflectionException
     */
    public function loadAllControllersInDirectoryAsCI($directory, $urlBase = '', $controllerNamespaceBase = '', $middleware = '')
    {
        if (!file_exists($directory) || !is_dir($directory)) {
            if ($this->debug) {
                echo __METHOD__ . " warning: this is not a direcoty: " . $directory . PHP_EOL;
            }
            return;
        }
        if ($handle = opendir($directory)) {
            if (
                $this->default_controller_name
                && file_exists($directory . '/' . $this->default_controller_name . '.php')
                && $this->default_method_name
                && method_exists($controllerNamespaceBase . $this->default_controller_name, $this->default_method_name)
            ) {
                $this->any(
                    $urlBase . '?',
                    [$controllerNamespaceBase . $this->default_controller_name, $this->default_method_name],
                    $middleware
                );
            }
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if (is_dir($directory . '/' . $entry)) {
                        //DIR,
                        $this->loadAllControllersInDirectoryAsCI(
                            $directory . '/' . $entry,
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
                            $this->any(
                                $urlBase . $name . '/?',
                                [$controllerNamespaceBase . $name, $this->default_method_name],
                                $middleware
                            );
                        }
                        $this->loadController(
                            $urlBase . $name . '/',
                            $controllerNamespaceBase . $name,
                            $middleware
                        );
                    }
                }
            }
            closedir($handle);
        }
    }
}
