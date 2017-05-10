<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 10:55
 */

namespace sinri\enoch\test\routing\middleware;


use sinri\enoch\mvc\MiddlewareInterface;

class SampleMiddleware extends MiddlewareInterface
{
    public function shouldAcceptRequest($path, $method, $params)
    {
        //print_r([$path,$method,$params]);
        if ($params[1] == 0) {
            return false;
        }
        return true;
    }
}