<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/24
 * Time: 13:31
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibRequest;
use sinri\enoch\helper\CommonHelper;

/**
 * Class Zillah
 * @since 2.1.4
 * Adah alike Router implements TREE-Parser.
 * Not used in Lamech by default.
 * @package sinri\enoch\mvc
 */
class Zillah extends RouterInterface
{
    const ROUTE_PARAM_METHOD = "METHOD";
    const ROUTE_PARAM_PATH = "PATH";
    const ROUTE_PARAM_CALLBACK = "CALLBACK";
    const ROUTE_PARAM_MIDDLEWARE = "MIDDLEWARE";
    const ROUTE_PARAM_NAMESPACE = "NAMESPACE";// only used in `group`

    const ROUTE_PARSED_PARAMETERS = "PARSED";// only used in sought result

    public function seekRoute($path, $method)
    {
        // a possible fix in 2.1.4
        if (strlen($path) > 1 && substr($path, strlen($path) - 1, 1) == '/') {
            $path = substr($path, 0, strlen($path) - 1);
        }
        return $this->seekRouteByTree($path, $method);
    }

    protected function seekRouteByTree($path, $method)
    {
        if ($this->debug) {
            echo __METHOD__ . ' path=' . $path . ' method=' . $method . PHP_EOL;
        }
        if ($path[0] == '/') $path = substr($path, 1);

        $components = explode("/", $path);
        $params = [];
        $route = $this->seekRouteByTreeForKeychain($this->routes, $components, $method, $params);
        if ($route) {
            $route[self::ROUTE_PARSED_PARAMETERS] = $params;
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

    /**
     * @since 1.4.2
     * @param $path
     * @param $callback
     * @param null $middleware
     */
    public function any($path, $callback, $middleware = null)
    {
        $this->registerRoute(LibRequest::METHOD_ANY, $path, $callback, $middleware);
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
     * @since 1.3.1
     * @param $shared
     * @param $list
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
            if (!empty($parameters)) {
                foreach ($parameters as $param) {
                    if ($param->isDefaultValueAvailable()) {
                        $path .= '/{' . $param->name . '}';
                    } else {
                        $path .= '/{' . $param->name . '?}';
                    }
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
                $urlBaseX = $urlBase;
                if (strlen($urlBaseX) > 0) {
                    $urlBaseX = substr($urlBaseX, 0, strlen($urlBaseX) - 1);
                }
                $this->any(
                    $urlBaseX,
                    [$controllerNamespaceBase . $this->default_controller_name, $this->default_method_name],
                    $middleware
                );
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
                            $urlBaseX = $urlBase . $name;
                            $this->any(
                                $urlBaseX,
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

    protected function registerRoute($method, $path, $callback, $middleware = null)
    {
        if ($this->debug) echo __METHOD__ . " : " . json_encode([$method, $path, $callback, $middleware]) . PHP_EOL;
        $path_components = explode("/", $path);
        $components = [];
        for ($i = 0; $i < count($path_components); $i++) {
            if (preg_match('/\{([A-Za-z0-9_]+)(\?)?\}/', $path_components[$i], $matches)) {
                // for variable component
                $x = "?";
                if (isset($matches[2])) $x .= $matches[2];
                $x .= (($i + 1 >= count($path_components)) ? "" : "/");
            } else {
                $x = $path_components[$i] . (($i + 1 >= count($path_components)) ? "" : "/");
            }
            $components[] = $x;
        }
        if (empty($method)) $method = LibRequest::METHOD_ANY;
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
        if ($this->debug) $object['debug'] = $components;
        CommonHelper::safeWriteNDArray($this->routes, $components, $object);
    }
}