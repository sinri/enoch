<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/2
 * Time: 14:12
 */

require_once __DIR__ . '/../../autoload.php';

$cache = new \sinri\enoch\service\FileCache(__DIR__ . '/cache_dir');

$cache->saveObject('A', 'Apple', 5);
$a = $cache->getObject('A');
echo "[should be Apple]: " . $a . PHP_EOL;

$cache->saveObject('A', "Amazing", 3);
$a = $cache->getObject('A');
echo "[should be Amazing]: " . $a . PHP_EOL;

sleep(5);
$a = $cache->getObject('A');
echo "[should be FALSE]: " . json_encode($a) . PHP_EOL;