<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 10:26
 */

require_once __DIR__ . '/../autoload.php';

require_once __DIR__.'/sample/SampleEnoch.php';

date_default_timezone_set("Asia/Shanghai");

$enoch=new \sinri\enoch\test\sample\SampleEnoch();
$enoch->start();
