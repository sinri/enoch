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
     * @param string $class_name such as sinri\enoch\test\routing\controller\SampleHandler
     * @param string $base_namespace such as sinri\enoch
     * @param string $base_path /code/sinri/enoch
     * @param string $extension
     * @return null|string
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


    const REQUEST_NO_ERROR = 0;
    const REQUEST_FIELD_NOT_FOUND = 1;
    const REQUEST_REGEX_NOT_MATCH = 2;

    /**
     * @param $target
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function safeReadArray($target, $name, $default = null, $regex = null, &$error = 0)
    {
        $error = self::REQUEST_NO_ERROR;
        if (!isset($target[$name])) {
            $error = self::REQUEST_FIELD_NOT_FOUND;
            return $default;
        }
        $value = $target[$name];
        if ($regex === null) {
            return $value;
        }
        if (!preg_match($regex, $value)) {
            $error = self::REQUEST_REGEX_NOT_MATCH;
            return $default;
        }
        return $value;
    }
}