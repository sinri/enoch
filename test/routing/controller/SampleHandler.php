<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/25
 * Time: 15:59
 */

namespace sinri\enoch\test\routing\controller;


use sinri\enoch\mvc\ApiInterface;

class SampleHandler extends ApiInterface
{
    protected $inner = 'lalala';

    public function handleCommonRequest($parts = [])
    {
        echo __METHOD__ . PHP_EOL;
        echo "(" . implode(',', $parts) . ")" . PHP_EOL;
        echo $this->inner . PHP_EOL;
    }

    public function handleErrorRequest()
    {
        echo __METHOD__;
    }

    public function handleGetRequest()
    {
        echo "Method: " . $this->spirit->getRequestMethod() . '; ' . __METHOD__;
    }

    public function handlePostRequest()
    {
        echo "Method: " . $this->spirit->getRequestMethod() . '; ' . __METHOD__;
    }

    public function handleOtherRequest()
    {
        echo "Method: " . $this->spirit->getRequestMethod() . '; ' . __METHOD__;
    }

    public function adah($p, $q)
    {
        echo __METHOD__ . " p=$p, q=$q";
    }
}