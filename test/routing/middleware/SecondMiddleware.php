<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/6/29
 * Time: 10:40
 */

namespace sinri\enoch\test\routing\middleware;


use sinri\enoch\mvc\MiddlewareInterface;

class SecondMiddleware extends MiddlewareInterface
{
    public function shouldAcceptRequest($path, $method, $params, &$preparedData = null, &$responseCode = 200, &$error = null)
    {
        if ($preparedData == 50) {
            return false;
        }
        return true;
    }
}