<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 23:20
 */

namespace sinri\enoch\helper;


use sinri\enoch\core\LibLog;
use sinri\enoch\core\LibRequest;
use sinri\enoch\mvc\BaseCodedException;

class CommonHelper
{
    /**
     * 按照PSR-0规范
     * @since 2.0.0 turn to static
     * @param string $class_name such as sinri\enoch\test\routing\controller\SampleHandler
     * @param string $base_namespace such as sinri\enoch
     * @param string $base_path /code/sinri/enoch
     * @param string $extension
     * @return null|string
     */
    public static function getFilePathOfClassNameWithPSR0($class_name, $base_namespace, $base_path, $extension = '.php')
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
    const REQUEST_SOURCE_ERROR = 3;

    /**
     * @since 2.0.0 turn to static
     * @param $target
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public static function safeReadArray($target, $name, $default = null, $regex = null, &$error = 0)
    {
        $error = self::REQUEST_NO_ERROR;

        // @since 1.3.7
        if (empty($target) || !is_array($target)) {
            $error = self::REQUEST_SOURCE_ERROR;
            return $default;
        }

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

    /**
     * Safe read ND-Array with keychain
     * @since 2.0.0 turn to static
     * @param array $array
     * @param array $keychain
     * @param mixed $default
     * @param null|string $regex
     * @param int $error
     * @return mixed|null
     */
    public static function safeReadNDArray($array, $keychain, $default = null, $regex = null, &$error = 0)
    {
        if (!is_array($keychain)) {
            return self::safeReadArray($array, $keychain, $default, $regex, $error);
        }
        $headKey = array_shift($keychain);
        if (empty($keychain)) {
            return self::safeReadArray($array, $headKey, $default, $regex, $error);
        }
        $sub_array = self::safeReadArray($array, $headKey, [], null, $error);
        if ($error !== self::REQUEST_NO_ERROR) {
            return $default;
        }
        return self::safeReadNDArray($sub_array, $keychain, $default, $regex, $error);
    }

    /**
     * @since 2.0.0 turn to static
     * @param $method
     * @param $url
     * @param null|array|string $data
     * @param array $headers
     * @param array $cookies
     * @param bool $bodyAsJson
     * @param null|LibLog $logger
     * @return mixed
     */
    public static function executeCurl($method, $url, $data = null, $headers = [], $cookies = [], $bodyAsJson = false, $logger = null)
    {
        //$logger = $this->getFileLogger();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $use_body = in_array($method, [LibRequest::METHOD_POST, LibRequest::METHOD_PUT]);
        if ($use_body) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data !== null) {
            if ($use_body) {
                $body = $data;
                if (is_array($data)) {
                    if ($bodyAsJson) {
                        $json_body_header = 'Content-Type: application/json';
                        if (!in_array($json_body_header, $headers)) {
                            $headers[] = $json_body_header;
                        }
                        $body = json_encode($data);
                    } else {
                        $body = http_build_query($data);
                    }
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            } else {
                $body = null;
                $query_string = http_build_query($data);
                if (!empty($query_string)) {
                    $url .= "?" . $query_string;
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($cookies)) {
            curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookies));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($logger) $logger->log(LibLog::LOG_INFO, "CURL-{$method}-Request", ["URL" => $url, "HEADER" => $headers, "BODY" => $data]);

        $response = curl_exec($ch);

        if ($logger) $logger->log(LibLog::LOG_INFO, "CURL-{$method}-Response", $response);

        curl_close($ch);
        return $response;
    }

    /**
     * @since 2.0.0 turn to static
     * @param $object
     * @param null $exception_error
     * @throws BaseCodedException
     */
    public static function assertNotEmpty($object, $exception_error = null)
    {
        if (empty($object)) {
            if ($exception_error === null) {
                $exception_error = __FUNCTION__;
            }
            throw new BaseCodedException($exception_error, BaseCodedException::ASSERT_FAILED);
        }
    }

    /**
     * @since 2.0.0 turn to static
     * @param array $array
     * @param $key
     * @param null $exception_error
     * @throws BaseCodedException
     * @internal param $object
     */
    public static function assertArrayItemNotEmpty($array, $key, $exception_error = null)
    {
        if (!is_array($array) || !isset($array[$key]) || empty($array[$key])) {
            if ($exception_error === null) {
                $exception_error = __FUNCTION__;
            }
            throw new BaseCodedException($exception_error, BaseCodedException::ASSERT_FAILED);
        }
    }

    /**
     * @since 2.0.0 turn to static
     * @param object $object
     * @param $key
     * @param null $exception_error
     * @throws BaseCodedException
     * @internal param $object
     */
    public static function assertObjectItemNotEmpty($object, $key, $exception_error = null)
    {
        if (!is_object($object) || !isset($object->$key) || empty($object->$key)) {
            if ($exception_error === null) {
                $exception_error = __FUNCTION__;
            }
            throw new BaseCodedException($exception_error, BaseCodedException::ASSERT_FAILED);
        }
    }

    /**
     * @since 2.0.0 turn to static
     * @param $list
     * @param $keyField
     * @return array
     * @throws BaseCodedException
     */
    public static function turnListToMapping($list, $keyField)
    {
        if (empty($list) || !is_array($list)) {
            return [];
        }
        $map = [];
        foreach ($list as $key => $item) {
            if (!isset($item[$keyField])) {
                throw new BaseCodedException("Key Field not exists");
            }
            $map[$item[$keyField]] = $item;
        }
        return $map;
    }
}