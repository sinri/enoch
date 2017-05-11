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

$lamech->useNaamahAsRouter();

// the following two lines hold the same effect as index
//$lamech->getRouter()->addRouteForFunction('/^\/$/', ["SampleHandler", "handleCommonRequest"]);
//$lamech->getRouter()->addRouteForFunction('', ["SampleHandler", "handleCommonRequest"]);
$lamech->getRouter()->get("", ["SampleHandler", "handleCommonRequest"]);

//$lamech->getRouter()->addRouteForFunction('/^\/closure$/', function ($parts) {
//    echo __METHOD__ . PHP_EOL;
//    echo __FILE__ . '@' . __LINE__ . PHP_EOL;
//    var_dump($parts);
//});
//$lamech->getRouter()->addRouteForView('/^\/error_page(\/.+\/.+\/.+)?$/', "ErrorPage");

$lamech->getRouter()->get('/^\/closure$/', function ($parts) {
    echo __METHOD__ . PHP_EOL;
    echo __FILE__ . '@' . __LINE__ . PHP_EOL;
    var_dump($parts);
});

//since v1.2.1 method support
$lamech->getRouter()->get(
    '/^\/method_test$/',
    ["SampleHandler", "handleGetRequest"]
);
$lamech->getRouter()->post(
    '/^\/method_test$/',
    ["SampleHandler", "handlePostRequest"]
);
$lamech->getRouter()->delete(
    '/^\/method_test$/',
    ["SampleHandler", "handleOtherRequest"]
);
$lamech->getRouter()->put(
    '/^\/method_test$/',
    ["SampleHandler", "handleOtherRequest"]
);

// use simple text other than regex to show route, you should remove the leading '/'
$lamech->getRouter()->get(
    'simple_url',
    ["SampleHandler", "handleCommonRequest"]
);

// pass the elements within the path to target
$lamech->getRouter()->get(
    '/^\/simple_url\/\d+(\/\d+)?$/',
    ["SampleHandler", "handleCommonRequest"]
);

// if you want to use customized error display
//$lamech->getRouter()->setErrorHandler(__DIR__.'/view/ErrorPage.php');
// Or,
//$lamech->getRouter()->setErrorHandler(function($error_info){
//    var_dump($error_info);
//});

$lamech->handleRequestWithRoutes("\\sinri\\enoch\\test\\routing\\controller\\");