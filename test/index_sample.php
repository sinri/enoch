<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 17:01
 */
require_once __DIR__ . '/../autoload.php';
//$spirit=\sinri\enoch\core\Spirit::getInstance();
//$all_get_list=$spirit->fullGetFields();
//$spirit->displayPage(__DIR__ . '/sample/SampleTemplate.php', ["list"=>$all_get_list]);

$lamech = new  \sinri\enoch\mvc\Lamech();

// If you want to use customized error page, set this line
$lamech->setErrorPage(__DIR__ . '/sample/error.php');

// If you want to use file-based session management, you should run these two lines
$lamech->setSessionDir('S_DIR');
$lamech->startSession();

// If you want to use controller as api
$lamech->setControllerDir(__DIR__ . '/sample');
$lamech->apiFromRequest("\\sinri\\enoch\\test\\sample\\");
// Or, if you want to use controller as page router
$lamech->setViewDir(__DIR__ . '/sample');
$lamech->viewFromRequest();