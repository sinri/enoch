<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/19
 * Time: 20:49
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../autoload.php';

$host = '';
$password = '';
$port = 6379;
$database = 255;
$cache = new \sinri\enoch\service\RedisCache($host, $port, $database, $password);

$cache->saveObject('A', 'Apple', 5);
$a = $cache->getObject('A');
echo "[should be Apple]: " . $a . PHP_EOL;

$cache->saveObject('A', "Amazing", 3);
$a = $cache->getObject('A');
echo "[should be Amazing]: " . $a . PHP_EOL;

sleep(5);
$a = $cache->getObject('A');
echo "[should be FALSE]: " . json_encode($a) . PHP_EOL;