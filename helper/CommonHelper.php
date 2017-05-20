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

    /**
     * @since 1.3.6
     * @param $method
     * @param $url
     * @param null|array|string $data
     * @param array $headers
     * @param array $cookies
     * @param bool $bodyAsJson
     * @param null|LibLog $logger
     * @return mixed
     */
    public function executeCurl($method, $url, $data = null, $headers = [], $cookies = [], $bodyAsJson = false, $logger = null)
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
     * @since 1.3.6
     * @param $object
     * @param null $exception_error
     * @throws BaseCodedException
     */
    public function assertNotEmpty($object, $exception_error = null)
    {
        if (empty($object)) {
            if ($exception_error === null) {
                $exception_error = __FUNCTION__;
            }
            throw new BaseCodedException($exception_error);
        }
    }

    /**
     * @since 1.3.6
     * @param array $array
     * @param $key
     * @param null $exception_error
     * @throws BaseCodedException
     * @internal param $object
     */
    public function assertArrayItemNotEmpty($array, $key, $exception_error = null)
    {
        if (!is_array($array) || !isset($array[$key]) || empty($array[$key])) {
            if ($exception_error === null) {
                $exception_error = __FUNCTION__;
            }
            throw new BaseCodedException($exception_error);
        }
    }

    /**
     * @since 1.3.6
     * @param object $object
     * @param $key
     * @param null $exception_error
     * @throws BaseCodedException
     * @internal param $object
     */
    public function assertObjectItemNotEmpty($object, $key, $exception_error = null)
    {
        if (!is_object($object) || !isset($object->$key) || empty($object->$key)) {
            if ($exception_error === null) {
                $exception_error = __FUNCTION__;
            }
            throw new BaseCodedException($exception_error);
        }
    }
}