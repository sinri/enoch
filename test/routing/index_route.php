<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/25
 * Time: 15:58
 */

require_once __DIR__ . '/../../autoload.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

$lamech->setControllerDir(__DIR__ . '/controller');
$lamech->setViewDir(__DIR__ . '/view');

$lamech->getRouter()->addRouteForFunction('/^\/$/', ["SampleHandler", "handleCommonRequest"]);
$lamech->getRouter()->addRouteForFunction('/^\/closure$/', function ($parts) {
    echo __METHOD__ . PHP_EOL;
    echo __FILE__ . '@' . __LINE__ . PHP_EOL;
    var_dump($parts);
});
$lamech->getRouter()->addRouteForView('/^\/error_page(\.+\.+\.+)?$/', "ErrorPage");

// if you want to use customized error display
//$lamech->getRouter()->setErrorHandler(__DIR__.'/view/ErrorPage.php');
// Or,
//$lamech->getRouter()->setErrorHandler(function($error_info){
//    var_dump($error_info);
//});

$lamech->handleRequestWithRoutes("\\sinri\\enoch\\test\\routing\\controller\\");