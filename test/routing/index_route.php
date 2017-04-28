<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/25
 * Time: 15:58
 */

if (!defined("GATEWAY_NAME")) die("GATEWAY_NAME not defined");

require_once __DIR__ . '/../../autoload.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

$lamech->setControllerDir(__DIR__ . '/controller');
$lamech->setViewDir(__DIR__ . '/view');

// the following two lines hold the same effect as index
//$lamech->getRouter()->addRouteForFunction('/^\/$/', ["SampleHandler", "handleCommonRequest"]);
$lamech->getRouter()->addRouteForFunction('', ["SampleHandler", "handleCommonRequest"]);

$lamech->getRouter()->addRouteForFunction('/^\/closure$/', function ($parts) {
    echo __METHOD__ . PHP_EOL;
    echo __FILE__ . '@' . __LINE__ . PHP_EOL;
    var_dump($parts);
});
$lamech->getRouter()->addRouteForView('/^\/error_page(\.+\.+\.+)?$/', "ErrorPage");

//since v1.1.0 method support
$lamech->getRouter()->addRouteForFunction(
    '/^\/method_test$/',
    ["SampleHandler", "handleGetRequest"],
    \sinri\enoch\core\Spirit::METHOD_GET
);
$lamech->getRouter()->addRouteForFunction(
    '/^\/method_test$/',
    ["SampleHandler", "handlePostRequest"],
    \sinri\enoch\core\Spirit::METHOD_POST
);
$lamech->getRouter()->addRouteForFunction(
    '/^\/method_test$/',
    ["SampleHandler", "handleOtherRequest"],
    \sinri\enoch\core\Spirit::METHOD_OPTION . "|" .
    \sinri\enoch\core\Spirit::METHOD_HEAD . "|" .
    \sinri\enoch\core\Spirit::METHOD_PUT . "|" .
    \sinri\enoch\core\Spirit::METHOD_DELETE
);

// use simple text other than regex to show route, you should remove the leading '/'
$lamech->getRouter()->addRouteForFunction(
    'simple_url',
    ["SampleHandler", "handleCommonRequest"],
    \sinri\enoch\core\Spirit::METHOD_GET
);

$lamech->getRouter()->addRouteForFunction(
    '/^\/simple_url\/\d+(\/\d+)?$/',
    ["SampleHandler", "handleCommonRequest"],
    \sinri\enoch\core\Spirit::METHOD_GET
);

// if you want to use customized error display
//$lamech->getRouter()->setErrorHandler(__DIR__.'/view/ErrorPage.php');
// Or,
//$lamech->getRouter()->setErrorHandler(function($error_info){
//    var_dump($error_info);
//});

$lamech->handleRequestWithRoutes("\\sinri\\enoch\\test\\routing\\controller\\");