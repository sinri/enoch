<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/28
 * Time: 22:29
 */
if (!defined("GATEWAY_NAME")) die("GATEWAY_NAME not defined");

require_once __DIR__ . '/../../autoload.php';
//autoload has covered these
//require_once __DIR__ . '/controller/SampleHandler.php';
//require_once __DIR__ . '/middleware/SampleMiddleware.php';

$lamech = new  \sinri\enoch\mvc\Lamech();

$lamech->setDebug(true);
//$lamech->getRouter()->setDebug(true);

$lamech->getRouter()->get("", ['\sinri\enoch\test\routing\controller\SampleHandler', 'handleCommonRequest']);
$lamech->getRouter()->get("adah/{p}/{q}", ['\sinri\enoch\test\routing\controller\SampleHandler', 'adah']);

$lamech->getRouter()->get(
    "mid/{p}/{q}",
    ['\sinri\enoch\test\routing\controller\SampleHandler', 'adah'],
    '\sinri\enoch\test\routing\middleware\SampleMiddleware'
);

$lamech->getRouter()->get(
    "default/{p}/{q?}",
    ['\sinri\enoch\test\routing\controller\SampleHandler', 'adah'],
    '\sinri\enoch\test\routing\middleware\SampleMiddleware'
);

//such as http://localhost/leqee/fundament/enoch/test/routing/group/add/3/4
$lamech->getRouter()->group(
    [
        \sinri\enoch\mvc\Adah::ROUTE_PARAM_MIDDLEWARE => '\sinri\enoch\test\routing\middleware\SampleMiddleware',
        \sinri\enoch\mvc\Adah::ROUTE_PARAM_PATH => 'group/',
        \sinri\enoch\mvc\Adah::ROUTE_PARAM_NAMESPACE => '\sinri\enoch\test\routing'
    ],
    [
        [
            \sinri\enoch\mvc\Adah::ROUTE_PARAM_PATH => 'add/{x}/{y}',
            \sinri\enoch\mvc\Adah::ROUTE_PARAM_METHOD => \sinri\enoch\core\LibRequest::METHOD_GET,
            \sinri\enoch\mvc\Adah::ROUTE_PARAM_CALLBACK => ['\controller\SampleHandler', 'groupAdd'],
        ],
        [
            \sinri\enoch\mvc\Adah::ROUTE_PARAM_PATH => 'minus/{x}/{y}',
            \sinri\enoch\mvc\Adah::ROUTE_PARAM_METHOD => \sinri\enoch\core\LibRequest::METHOD_GET,
            \sinri\enoch\mvc\Adah::ROUTE_PARAM_CALLBACK => ['\controller\SampleHandler', 'groupMinus'],
        ]
    ]
);

// such as http://localhost/leqee/fundament/enoch/test/routing/controller/groupAdd/4/4
$lamech->getRouter()->loadController(
    'controller/',
    '\sinri\enoch\test\routing\controller\SampleHandler',
    '\sinri\enoch\test\routing\middleware\SampleMiddleware'
);

$lamech->getRouter()->setDefaultControllerName('SampleHandler');
$lamech->getRouter()->loadAllControllersInDirectoryAsCI(
    __DIR__ . '/controller',
    'ci/',
    '\sinri\enoch\test\routing\controller\\',
    ['\sinri\enoch\test\routing\middleware\SampleMiddleware', '\sinri\enoch\test\routing\middleware\SecondMiddleware']
);

$lamech->handleRequest();

// e.g. for CLI
// php test/routing/index.php /ci/SethHandler/index 1
