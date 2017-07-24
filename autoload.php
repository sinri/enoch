<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 10:27
 */
require_once __DIR__ . '/helper/CommonHelper.php';
spl_autoload_register(function ($class_name) {
    $file_path = \sinri\enoch\helper\CommonHelper::getFilePathOfClassNameWithPSR0(
        $class_name,
        'sinri\enoch',
        __DIR__,
        '.php'
    );
    if ($file_path) {
        require_once $file_path;
    }
});