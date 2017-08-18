<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/8/18
 * Time: 12:45
 */

require_once __DIR__ . '/../../autoload.php';

$ftp_config = [];

require __DIR__ . '/config.php';


$ftper = new \sinri\enoch\core\LibFTP();

$ftper->setFtpServer($ftp_config['server']);
$ftper->setFtpPort($ftp_config['port']);
$ftper->setFtpUsername($ftp_config['username']);
$ftper->setFtpPassword($ftp_config['password']);

echo "REMOVE" . PHP_EOL;
$done = $ftper->deleteFileFromFTP("/htdocs/ftp_test.htm");
var_dump($done);

echo "PUT" . PHP_EOL;
$done = $ftper->sendFileToFTP(__DIR__ . '/ftp_test.htm', "/htdocs/ftp_test.htm");
var_dump($done);
