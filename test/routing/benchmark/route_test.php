<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/22
 * Time: 00:21
 */

define("GATEWAY_NAME", "index.php");
date_default_timezone_set("Asia/Shanghai");

require_once __DIR__ . '/../../../autoload.php';

$json = file_get_contents(__DIR__ . '/paths.json');
$json = json_decode($json, true);
$paths = $json['paths'];
$cases = $json['cases'];

foreach ([\sinri\enoch\mvc\Adah::ROUTER_TYPE_REGEX, \sinri\enoch\mvc\Adah::ROUTER_TYPE_TREE] as $type) {
    echo "TYPE " . $type . PHP_EOL;
    \sinri\enoch\mvc\Lamech::$routerType = $type;

    $lamech = new \sinri\enoch\mvc\Lamech();

    echo "COUNT ROUTE PATHS: " . count($paths) . PHP_EOL;

    $register_route_start = microtime(true);

    foreach ($paths as $path) {
        $lamech->getRouter()->any($path, function () use ($path) {
            echo $path . PHP_EOL;
        });
    }

    $register_route_end = microtime(true);

    $time_for_register = ($register_route_end - $register_route_start);
    echo "REGISTER SPENT " . $time_for_register . " s" . PHP_EOL;
    echo "PATHS: " . count($paths) . PHP_EOL;
    echo "REGISTER AVG: " . ($time_for_register * 1.0 / count($paths)) . " s" . PHP_EOL;

    $stat = ['done' => 0, 'fail' => 0];
    $process_start = microtime(true);
    foreach ($cases as $case) {
        try {
            $result = $lamech->getRouter()->seekRoute($case, \sinri\enoch\core\LibRequest::METHOD_GET);
        } catch (Exception $exception) {
            $result = false;
        }
        if ($result) {
            $stat['done']++;
        } else {
            $stat['fail']++;
        }
    }

    $process_end = microtime(true);
    $time_for_process = $process_end - $process_start;
    echo "PROCESS SPENT " . $time_for_process . " s" . PHP_EOL;
    echo "CASES: " . count($cases) . PHP_EOL;
    echo "PROCESS AVG: " . ($time_for_process * 1.0 / count($cases)) . " s" . PHP_EOL;

    echo "PROCESS STAT: " . json_encode($stat) . PHP_EOL;
}
