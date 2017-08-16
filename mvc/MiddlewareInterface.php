<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 10:21
 */

namespace sinri\enoch\mvc;


class MiddlewareInterface
{
    /**
     * @param $class_name string class name with namespace
     * @return MiddlewareInterface
     */
    public static function MiddlewareFactory($class_name)
    {
        if (!empty($class_name)) {
            return new $class_name();
        }
        return new MiddlewareInterface();
    }

    /**
     * Check request data with $_REQUEST, $_SESSION, $_SERVER, etc.
     * And decide if the request should be accepted.
     * If return false, the request would be thrown.
     * You can pass anything into $preparedData, that controller might use it (not sure, by the realization)
     * @param $path
     * @param $method
     * @param $params
     * @param null $preparedData @since 1.3.6
     * @return bool
     */
    public function shouldAcceptRequest($path, $method, $params, &$preparedData = null)
    {
        return true;
    }

    /**
     * You can use this as `hasPrefixAmong($path,['/AdminSession/login','/FileAgent/getFile/'])`
     * as the paths like '/AdminSession/login' and '/FileAgent/getFile/xxx' would return true.
     * Only pure string (case sensitive) would be taken as check rule.
     * Anyway, you may think about the shared prefix, such as '/AdminSession/loginAgain', returns true too.
     * But you should know, all the urls are designed by yourself, you can design them to avoid side effects.
     * @param $path
     * @param array $prefixList
     * @return bool
     */
    public static function hasPrefixAmong($path, $prefixList = [])
    {
        foreach ($prefixList as $prefix) {
            if (0 === strpos($path, $prefix)) {
                return true;
            }
        }
        return false;
    }
}