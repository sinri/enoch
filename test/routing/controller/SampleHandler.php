<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/25
 * Time: 15:59
 */

namespace sinri\enoch\test\routing\controller;


use sinri\enoch\core\LibLog;
use sinri\enoch\core\LibRequest;
use sinri\enoch\mvc\SethController;

class SampleHandler extends SethController
{
    protected $inner = 'lalala';
    protected $logger = null;

    public function __construct($initData = null)
    {
        parent::__construct($initData);
        $this->logger = new LibLog(__DIR__ . '/../log', 'SampleHandler');
    }

    public function index()
    {
        echo __METHOD__;
    }

    public function handleCommonRequest($parts = [])
    {
        echo __METHOD__ . PHP_EOL;
        echo "(" . implode(',', $parts) . ")" . PHP_EOL;
        echo $this->inner . PHP_EOL;
        echo LibRequest::get('k') . PHP_EOL;
    }

    public function handleErrorRequest()
    {
        echo __METHOD__;
    }

    public function handleGetRequest()
    {
        echo "Method: " . LibRequest::getRequestMethod() . '; ' . __METHOD__;
    }

    public function handlePostRequest()
    {
        echo "Method: " . LibRequest::getRequestMethod() . '; ' . __METHOD__;
    }

    public function handleOtherRequest()
    {
        echo "Method: " . LibRequest::getRequestMethod() . '; ' . __METHOD__;
    }

    public function adah($p, $q = 'Q')
    {
        echo __METHOD__ . " p=$p, q=$q";
        $this->logger->log(LibLog::LOG_INFO, __METHOD__, [$p, $q]);
    }

    public function groupAdd($x, $y)
    {
        echo "$x + $y = " . ($x + $y);
        //print_r(get_class_methods(__CLASS__));
    }

    public function groupMinus($x, $y)
    {
        echo "$x - $y = " . ($x - $y);
    }

    public function defulatValue($a, $b = 4, $c = 5, $d = 6)
    {
        echo $a . $b . $c . $d;
    }
}