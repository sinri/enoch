<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/25
 * Time: 16:02
 */
require_once __DIR__ . '/../../autoload.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

// If you want to use file-based session management, you should run these two lines
//$lamech->setSessionDir('S_DIR');
//$lamech->startSession();

$lamech->setControllerDir(__DIR__ . '/controller');

$lamech->setDefaultControllerName("SampleHandler");
$lamech->setDefaultMethodName("handleErrorRequest");

$lamech->restfullyHandleRequest("\\sinri\\enoch\\test\\routing\\controller\\");