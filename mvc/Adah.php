<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/28
 * Time: 21:33
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibRequest;
use sinri\enoch\helper\CommonHelper;

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

    protected $routerType;
    const ROUTER_TYPE_TREE = 'TREE';
    const ROUTER_TYPE_REGEX = 'REGEX';

    /**
     * Adah constructor.
     * @param string $type TREE | REGEX
     */
    public function __construct($type = Adah::ROUTER_TYPE_REGEX)
    {
        parent::__construct();
        $this->routerType = $type;
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
    protected function registerRouteWithRegex($method, $path, $callback, $middleware = null)
    {
        if ($this->debug) {
            echo __METHOD__ . "(" . json_encode($method) . ", " . json_encode($path) . ", " . json_encode($callback) . ", " . json_encode($middleware) . PHP_EOL;
        }

        /*
        // METHOD 1
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

    protected function registerRouteWithTree($method, $path, $callback, $middleware = null)
    {
        if ($this->debug) {
            echo __METHOD__ . "(" . json_encode($method) . ", " . json_encode($path) . ", " . json_encode($callback) . ", " . json_encode($middleware) . PHP_EOL;
        }
        $path_components = explode("/", $path);
        //var_dump($path_components);
        $components = [];//["/"];
        for ($i = 0; $i < count($path_components); $i++) {
            if (preg_match('/\{([A-Za-z0-9_]+)(\?)?\}/', $path_components[$i], $matches)) {
                //this is parameter
                $x = "?";
                if (isset($matches[2])) {
                    $x .= $matches[2];
                }
                $x .= (($i + 1 >= count($path_components)) ? "" : "/");
            } else {
                $x = $path_components[$i] . (($i + 1 >= count($path_components)) ? "" : "/");
            }
            $components[] = $x;
        }
        if (empty($method)) {
            $method = LibRequest::METHOD_ANY;
        }
        $this->addTreeRouteItem($components, $method, [
            self::ROUTE_PARAM_CALLBACK => $callback,
            self::ROUTE_PARAM_MIDDLEWARE => $middleware,
            self::ROUTE_PARAM_METHOD => $method,
        ]);
    }

    protected function addTreeRouteItem($components, $method, $object)
    {
        /*
        $route_sample = [
            ""=>["ANY" => ["callback" => ['class', 'method'], 'middleware' => 'class']],
            // key for top level must be `/`
            "/" => [
                // key for children level might be
                // I. `xxx/` with children
                // II. `?/` with children, as parameter
                // III. `xxx` without children
                // IV. `?` without children, as parameter
                "level1" => ["GET" => ["callback" => ['class', 'method']]]
            ]
        ];
        */
        $components[] = $method;
        $object['debug'] = $components;
        CommonHelper::safeWriteNDArray(
            $this->routes,
            $components,
            $object
        );
    }

    protected function registerRoute($method, $path, $callback, $middleware = null)
    {
        if ($this->routerType === Adah::ROUTER_TYPE_TREE) {
            $this->registerRouteWithTree($method, $path, $callback, $middleware);
        } else {//Adah::ROUTER_TYPE_REGEX
            $this->registerRouteWithRegex($method, $path, $callback, $middleware);
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

    public function seekRoute($path, $method)
    {
        if ($this->routerType === Adah::ROUTER_TYPE_TREE) {
            return $this->seekRouteByTree($path, $method);
        } else {//Adah::ROUTER_TYPE_REGEX
            return $this->seekRouteByRegex($path, $method);
        }
    }

    protected function seekRouteByTree($path, $method)
    {
        if ($this->debug) {
            echo __METHOD__ . ' path=' . $path . ' method=' . $method . PHP_EOL;
            //print_r($this->routes);
            echo PHP_EOL;
        }
        if ($path[0] == '/') $path = substr($path, 1);

        $components = explode("/", $path);
        $params = [];
        $route = $this->seekRouteByTreeForKeychain($this->routes, $components, $method, $params);
        if ($route) {
            $route[self::ROUTE_PARSED_PARAMETERS] = $params;
            //var_dump($route);
            return $route;
        }
        throw new BaseCodedException(
            "No route matched: path={$path} method={$method}",
            BaseCodedException::NO_MATCHED_ROUTE
        );
    }

    protected function seekRouteByTreeForKeychain($tree, $keychain, $method, &$params = [])
    {
        if ($this->debug) {
            echo __METHOD__ .
                ' tree nodes: ' . json_encode(array_keys($tree)) .
                " keychain: " . json_encode($keychain) .
                " method: " . $method .
                " parmas: " . json_encode($params) . PHP_EOL;
        }
        $headKey = array_shift($keychain);
        if (empty($keychain)) {
            //the keychain's final component
            if (isset($tree[$headKey])) {
                $found = $headKey;
            } elseif (isset($tree["?"])) {
                $found = "?";
                $params[] = $headKey;
            } elseif (isset($tree["??"])) {
                $found = "??";
                $params[] = $headKey;
            } elseif (isset($tree["?/"])) {
                $found = "?/";
                $params[] = $headKey;
                // url is over but mapping is not
                do {
                    $tree = $tree[$found];
                    if (!is_array($tree)) {
                        throw new BaseCodedException("PARSE ROUTE ERROR " . __LINE__);
                    }
                    $found = array_keys($tree);
                    if (empty($found)) {
                        throw new BaseCodedException("PARSE ROUTE ERROR " . __LINE__);
                    }
                    $found = $found[0];
                } while (substr($found, strlen($found) - 1) == '/');
            } elseif (isset($tree["??/"])) {
                $found = "??/";
                $params[] = $headKey;
                // url is over but mapping is not
                do {
                    $tree = $tree[$found];
                    if (!is_array($tree)) {
                        throw new BaseCodedException("PARSE ROUTE ERROR " . __LINE__);
                    }
                    $found = array_keys($tree);
                    if (empty($found)) {
                        throw new BaseCodedException("PARSE ROUTE ERROR " . __LINE__);
                    }
                    $found = $found[0];
                } while (substr($found, strlen($found) - 1) == '/');
            } else {
                return false;
            }

            //var_dump($tree);
            //var_dump($found);

            if (isset($tree[$found][$method])) {
                // special method
                return $tree[$found][$method];
            } elseif (isset($tree[$found][LibRequest::METHOD_ANY])) {
                return $tree[$found][LibRequest::METHOD_ANY];
            } else {
                return false;
            }
        }
        // for the situation that keychain is still holding tails, do DFS
        if (isset($tree[$headKey . '/'])) {
            $found = $headKey . '/';
            $route = $this->seekRouteByTreeForKeychain($tree[$found], $keychain, $method, $params);
            if ($route) {
                return $route;
            }
        }
        if (isset($tree["?/"])) {
            $params[] = $headKey;
            $found = "?/";
            $route = $this->seekRouteByTreeForKeychain($tree[$found], $keychain, $method, $params);
            if ($route) {
                return $route;
            }
        }
        if (isset($tree["??/"])) {
            $params[] = $headKey;
            $found = "??/";
            $route = $this->seekRouteByTreeForKeychain($tree[$found], $keychain, $method, $params);
            if ($route) {
                return $route;
            }
        }

        //not found
        return false;
    }

    protected function seekRouteByRegex($path, $method)
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
                if ($this->routerType === self::ROUTER_TYPE_TREE) {
                    foreach ($parameters as $param) {
                        if ($param->isDefaultValueAvailable()) {
                            $path .= '/{' . $param->name . '}';
                        } else {
                            $path .= '/{' . $param->name . '?}';
                        }
                    }
                } else {
                    //self::ROUTER_TYPE_REGEX
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
     */
    public function loadAllControllersInDirectoryAsCI($directory, $urlBase = '', $controllerNamespaceBase = '', $middleware = '')
    {
        if ($handle = opendir($directory)) {
            if (
                $this->default_controller_name
                && file_exists($directory . '/' . $this->default_controller_name . '.php')
                && $this->default_method_name
                && method_exists($controllerNamespaceBase . $this->default_controller_name, $this->default_method_name)
            ) {
                if ($this->routerType === Adah::ROUTER_TYPE_TREE) {
                    $urlBaseX = $urlBase;
                    if (strlen($urlBaseX) > 0) {
                        $urlBaseX = substr($urlBaseX, 0, strlen($urlBaseX) - 1);
                    }
                    $this->any(
                        $urlBaseX,
                        [$controllerNamespaceBase . $this->default_controller_name, $this->default_method_name],
                        $middleware
                    );
                } else {
                    $this->any(
                        $urlBase . '?',
                        [$controllerNamespaceBase . $this->default_controller_name, $this->default_method_name],
                        $middleware
                    );
                }
            }
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if (is_dir($directory . '/' . $entry)) {
                        //DIR,
                        $this->loadAllControllersInDirectoryAsCI(
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
                            if ($this->routerType === Adah::ROUTER_TYPE_TREE) {
                                $urlBaseX = $urlBase . $name;
                                $this->any(
                                    $urlBaseX,
                                    [$controllerNamespaceBase . $name, $this->default_method_name],
                                    $middleware
                                );
                            } else {
                                $this->any(
                                    $urlBase . $name . '/?',
                                    [$controllerNamespaceBase . $name, $this->default_method_name],
                                    $middleware
                                );
                            }
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
