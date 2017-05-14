<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/14
 * Time: 19:24
 */

date_default_timezone_set("Asia/Shanghai");
require_once __DIR__ . '/../../autoload.php';

$event_listener1 = new \sinri\enoch\service\EventListener('EL1', function ($params = []) {
    echo "This is EL1: " . json_encode($params) . PHP_EOL;
});

$event_listener2 = new \sinri\enoch\service\EventListener('EL2', function ($params = []) {
    echo "This is EL2: " . json_encode($params) . PHP_EOL;
});

$event_agent = new \sinri\enoch\service\EventAgent();
$event_agent->registerEventListener($event_listener1);
$event_agent->registerEventListener($event_listener2);

for ($i = 0; $i < 100; $i++) {
    if ($i == 30) {
        $event_agent->fire("EL1", ["i" => $i]);
    } elseif ($i == 70) {
        $event_agent->fire("EL2", ["i" => $i]);
    }
}