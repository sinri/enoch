<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/23
 * Time: 20:35
 */
require_once __DIR__ . '/../../autoload.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

// If you want to use customized error page, set this line
$lamech->setErrorPage(__DIR__ . '/ErrorPage.php');

// If you want to use file-based session management, you should run these two lines
//$lamech->setSessionDir('S_DIR');
//$lamech->startSession();

$lamech->setViewDir(__DIR__);

// If you want to use controller as api
$lamech->setControllerDir(__DIR__ . '/controller');
//$lamech->apiFromRequest("\\sinri\\enoch\\test\\requesting\\");

$lamech->setDefaultControllerName("ExampleAPI");
$lamech->setDefaultMethodName("index");

// If you like CI
//$lamech->restfullyHandleRequest("\\sinri\\enoch\\test\\requesting\\controller\\");

// If you like flight
//$lamech->getRouter()->addRouteForClass('/',"\\sinri\\enoch\\test\\requesting\\controller\\ExampleAPI");
$lamech->getRouter()->addRouteForFunction('/^\/$/', ["ExampleAPI", "index"]);
$lamech->getRouter()->addRouteForFunction('/^\/closure$/', function ($parts) {
    echo __METHOD__ . PHP_EOL;
    echo __FILE__ . '@' . __LINE__ . PHP_EOL;
    var_dump($parts);
});
$lamech->getRouter()->addRouteForView('/^\/error_page(\.+\.+\.+)?$/', "ErrorPage");

$lamech->handleRequestWithRoutes("\\sinri\\enoch\\test\\requesting\\controller\\");