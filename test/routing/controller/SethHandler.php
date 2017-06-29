<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/6/29
 * Time: 10:51
 */

namespace sinri\enoch\test\routing\controller;


use sinri\enoch\mvc\SethInterface;

class SethHandler implements SethInterface
{

    public function __construct($initData = null)
    {
        echo __METHOD__ . " Init Data: " . json_encode($initData) . PHP_EOL;
    }

    public function index($a = 1, $b = 2)
    {
        echo __METHOD__ . "({$a},{$b})" . PHP_EOL;
    }
}