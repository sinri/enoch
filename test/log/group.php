<?php
require_once __DIR__ . '/../../autoload.php';

$logger = new \sinri\enoch\core\LibLog(__DIR__ . '/../../log', '', false, true);
$logger->log(\sinri\enoch\core\LibLog::LOG_INFO, "WeDieWithHonor!");

$logger = new \sinri\enoch\core\LibLog(__DIR__ . '/../../log', 'X', false, true);
$logger->log(\sinri\enoch\core\LibLog::LOG_INFO, "TheyDieWithHonor!");

$logger = new \sinri\enoch\core\LibLog(__DIR__ . '/../../log', '', false);
$logger->log(\sinri\enoch\core\LibLog::LOG_INFO, "YouDieWithHonor!");