<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/9/12
 * Time: 10:29
 */

require_once __DIR__ . '/../../autoload.php';

$logger = new \sinri\enoch\core\LibLog();

$logger->log(\sinri\enoch\core\LibLog::LOG_ERROR, "error");
$logger->log(\sinri\enoch\core\LibLog::LOG_WARNING, "warning");
$logger->log(\sinri\enoch\core\LibLog::LOG_INFO, "info");
$logger->log(\sinri\enoch\core\LibLog::LOG_DEBUG, "debug");

$logger->setIgnoreLevel(\sinri\enoch\core\LibLog::LOG_WARNING);

$logger->log(\sinri\enoch\core\LibLog::LOG_ERROR, "error visible");
$logger->log(\sinri\enoch\core\LibLog::LOG_WARNING, "warning visible");
$logger->log(\sinri\enoch\core\LibLog::LOG_INFO, "info not visible");
$logger->log(\sinri\enoch\core\LibLog::LOG_DEBUG, "debug not visible");