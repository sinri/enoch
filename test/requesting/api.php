<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/23
 * Time: 20:35
 */

require_once __DIR__ . '/../../autoload.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

$lamech->setErrorPage(__DIR__ . '/ErrorPage.php');

// If you want to use file-based session management, you should run these two lines
//$lamech->setSessionDir('S_DIR');
//$lamech->startSession();

// If you want to use controller as api
$lamech->setControllerDir(__DIR__ . '/controller');
$lamech->handleRequestAsApi("\\sinri\\enoch\\test\\requesting\\controller\\");
