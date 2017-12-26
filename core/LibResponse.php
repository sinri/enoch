<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 23:42
 */

namespace sinri\enoch\core;


use sinri\enoch\mvc\BaseCodedException;

class LibResponse
{
    const AJAX_JSON_CODE_OK = "OK";
    const AJAX_JSON_CODE_FAIL = "FAIL";

    /**
     * @param $anything
     */
    public static function json($anything)
    {
        echo json_encode($anything);
    }

    /**
     * @param string $code OK or FAIL
     * @param mixed $data
     */
    public static function jsonForAjax($code = self::AJAX_JSON_CODE_OK, $data = '')
    {
        echo json_encode(["code" => $code, "data" => $data]);
    }

    /**
     * @param $filepath
     * @param array $params
     * @throws BaseCodedException
     */
    public static function displayPage($filepath, $params = [])
    {
        extract($params);
        if (!file_exists($filepath)) {
            throw new BaseCodedException("View not fould.");
        }
        require $filepath;
    }

    /**
     * @param string $message
     * @param null $exception
     * @param null $viewPath
     * @throws BaseCodedException
     */
    public static function errorPage($message = '', $exception = null, $viewPath = null)
    {
        if (empty($viewPath) || !file_exists($viewPath) || !is_file($viewPath)) {
            echo "<h3>ERROR</h3><hr>" . PHP_EOL;
            echo "<pre>" . PHP_EOL;
            echo $message;
            echo PHP_EOL;
            if ($exception) {
                var_dump($exception);
            }
            echo "</pre>";
            echo "<hr>";
            echo "<p>Behold, the Lord cometh with ten thousands of his saints, to execute judgment upon all... (Jude 1:14-15)</p>" . PHP_EOL;
            echo "<p>Powered by Enoch Project</p>" . PHP_EOL;
            return;
        }
        self::displayPage($viewPath, ['message' => $message, "exception" => $exception]);
    }

    /**
     * 文件下载
     * @param $file
     * @param null $down_name
     * @param BaseCodedException $error
     * @param null $content_type
     * @return bool
     */
    public static function downloadFileAsName($file, $down_name = null, &$error = null, $content_type = null)
    {
        //判断给定的文件存在与否
        if (!file_exists($file)) {
            //throw new BaseCodedException("No such file there",BaseCodedException::RESOURCE_NOT_EXISTS);
            $error = new BaseCodedException("No such file there", BaseCodedException::RESOURCE_NOT_EXISTS);
            //"您要下载的文件已不存在，可能是被删除";
            return false;
        }

        if ($down_name !== null && $down_name !== false) {
            $suffix = substr($file, strrpos($file, '.')); //获取文件后缀
            $down_name = $down_name . $suffix; //新文件名，就是下载后的名字
        } else {
            $k = pathinfo($file);
            $down_name = $k['filename'] . '.' . $k['extension'];
        }

        $fp = fopen($file, "r");
        $file_size = filesize($file);

        if ($content_type === null) {
            $content_type = 'application/octet-stream';
        }

        //下载文件需要用到的头
        header("Content-Type: " . $content_type);
        header("Accept-Ranges: bytes");
        header("Accept-Length:" . $file_size);
        header("Content-Disposition: attachment; filename=" . $down_name);
        $buffer = 1024;
        $file_count = 0;
        //向浏览器返回数据
        while (!feof($fp) && $file_count < $file_size) {
            $file_con = fread($fp, $buffer);
            $file_count += $buffer;
            echo $file_con;
        }
        fclose($fp);
        return true;
    }
}