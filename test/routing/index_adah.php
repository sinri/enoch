<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/28
 * Time: 22:29
 */
if (!defined("GATEWAY_NAME")) die("GATEWAY_NAME not defined");

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/controller/SampleHandler.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

$lamech->setControllerDir(__DIR__ . '/controller');
$lamech->setViewDir(__DIR__ . '/view');

$lamech->useAdahAsRouter();

$lamech->getRouter()->get("", ['\sinri\enoch\test\routing\controller\SampleHandler', 'handleCommonRequest']);
$lamech->getRouter()->get("adah/{p}/{q}", ['\sinri\enoch\test\routing\controller\SampleHandler', 'adah']);

$lamech->handleRequestThroughAdah();