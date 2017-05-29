<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/28
 * Time: 22:34
 */

date_default_timezone_set("Asia/Shanghai");
require_once __DIR__ . '/../../autoload.php';

$baruch = new \sinri\enoch\mvc\Baruch();
$baruch->setStorage(__DIR__ . '/storage');
$baruch->setExtension(".md");
$baruch->handleWiki();