<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 17:01
 */
require_once __DIR__ . '/../autoload.php';
$spirit=\sinri\enoch\core\Spirit::getInstance();
$all_get_list=$spirit->fullGetFields();
$spirit->displayPage(__DIR__ . '/sample/SampleTemplate.php', ["list"=>$all_get_list]);
