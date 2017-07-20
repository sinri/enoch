<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/20
 * Time: 12:00
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

$key = __METHOD__;

$queue = new \sinri\enoch\service\RedisQueue($key, $host, $port, $database, $password);

$item = new \sinri\enoch\service\RedisQueueItem(["name" => 'Ein']);

echo "push " . json_encode($item) . PHP_EOL;
$done = $queue->addToQueueTail($item);
var_dump($done);

$item = new \sinri\enoch\service\RedisQueueItem(["name" => 'Zwie']);

echo "push " . json_encode($item) . PHP_EOL;
$done = $queue->addToQueueTail($item);
var_dump($done);

echo "pop one" . PHP_EOL;
$item = $queue->takeFromQueueHead();
if ($item) {
    $item->handle();
} else {
    echo "item is not available" . PHP_EOL;
}

echo "LENGTH=" . $queue->queueLength() . PHP_EOL;

for ($i = 0; $i < 3; $i++) {
    echo "index at " . $i . PHP_EOL;
    $item = $queue->objectAtIndex($i);
    if ($item) {
        $item->handle();
    } else {
        echo "item is not available" . PHP_EOL;
    }
}

echo "pop one" . PHP_EOL;
$item = $queue->takeFromQueueHead();
if ($item) {
    $item->handle();
} else {
    echo "item is not available" . PHP_EOL;
}