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

if (file_exists(__DIR__ . '/redis_config.php')) {
    require __DIR__ . '/redis_config.php';
}

$cache = new \sinri\enoch\service\RedisCache($host, $port, $database, $password);


// 0. you cannot set array to redis...
//$a = ["name" => "A", "value" => "ALPHA"];
//$cache->saveObject("A", $a);
//$a = $cache->getObject("A");
//echo "[should be an KV array]: " . json_encode($a) . PHP_EOL;
//
//$a = [123, 124, 3523];
//$cache->saveObject("A", $a);
//$a = $cache->getObject("A");
//echo "[should be an ordered array]: " . json_encode($a) . PHP_EOL;

// 1. common save

$cache->saveObject('A', 'Apple', 5);
$a = $cache->getObject('A');
echo "[should be Apple]: " . $a . PHP_EOL;

// 2. common remove

$cache->removeObject('A');
$a = $cache->getObject('A');
echo "[should be FALSE]: " . json_encode($a) . PHP_EOL;

// 3. special for redis INCR/DECR

$cache->increase('A', 1);
$a = $cache->getObject('A');
echo "[should be 1]: " . $a . PHP_EOL;

$cache->increase('A', 2);
$a = $cache->getObject('A');
echo "[should be 3]: " . $a . PHP_EOL;

$cache->decrease('A', 1);
$a = $cache->getObject('A');
echo "[should be 2]: " . $a . PHP_EOL;

$cache->increaseFloat('A', 1.5);
$a = $cache->getObject('A');
echo "[should be 3.5]: " . $a . PHP_EOL;

// 4. special for redis APPEND

$cache->saveObject('A', "Amazing", 3);
$a = $cache->getObject('A');
echo "[should be Amazing]: " . $a . PHP_EOL;

$cache->append('A', ' Grace');
$a = $cache->getObject('A');
echo "[should be Amazing Grace]: " . $a . PHP_EOL;

// 5. timeout

sleep(5);
$a = $cache->getObject('A');
echo "[should be FALSE]: " . json_encode($a) . PHP_EOL;
