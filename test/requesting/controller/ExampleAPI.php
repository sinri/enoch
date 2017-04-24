<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/23
 * Time: 20:34
 */

namespace sinri\enoch\test\requesting\controller;


use sinri\enoch\mvc\ApiInterface;

class ExampleAPI extends ApiInterface
{
    public function test($p1 = 1, $p2 = 2)
    {
        //print_r(func_get_args());
        echo __METHOD__ . " with P1=$p1, P2=$p2";
        //return __LINE__;
    }

    public function index($p1 = 1, $p2 = 2)
    {
        echo __METHOD__ . " with P1=$p1, P2=$p2";
    }
}