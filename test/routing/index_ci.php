<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/25
 * Time: 16:02
 */
if (!defined("GATEWAY_NAME")) die("GATEWAY_NAME not defined");

require_once __DIR__ . '/../../autoload.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

// If you want to use file-based session management, you should run these two lines
//$lamech->setSessionDir('S_DIR');
//$lamech->startSession();

$lamech->setControllerDir(__DIR__ . '/controller');

$lamech->setDefaultControllerName("SampleHandler");
$lamech->setDefaultMethodName("handleErrorRequest");

$lamech->handleRequestAsCI("\\sinri\\enoch\\test\\routing\\controller\\");