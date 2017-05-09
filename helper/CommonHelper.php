<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 23:20
 */

namespace sinri\enoch\helper;


class CommonHelper
{
    /**
     * 按照PSR-0规范
     * @since 1.2.9
     * @param $class_name string such as sinri\enoch\test\routing\controller\SampleHandler
     * @param $base_namespace string such as sinri\enoch
     * @param $base_path string /code/sinri/enoch
     * @return string|null
     */
    public function getFilePathOfClassNameWithPSR0($class_name, $base_namespace, $base_path, $extension = '.php')
    {
        if (strpos($class_name, $base_namespace) === 0) {
            $class_file = str_replace($base_namespace, $base_path, $class_name);
            $class_file .= $extension;
            $class_file = str_replace('\\', '/', $class_file);
            return $class_file;
        }
        return null;
    }
}