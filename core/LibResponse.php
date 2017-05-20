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
    public function json($anything)
    {
        echo json_encode($anything);
    }

    /**
     * @param string $code OK or FAIL
     * @param mixed $data
     */
    public function jsonForAjax($code = self::AJAX_JSON_CODE_OK, $data = '')
    {
        echo json_encode(["code" => $code, "data" => $data]);
    }

    /**
     * @param $filepath
     * @param array $params
     * @throws BaseCodedException
     */
    public function displayPage($filepath, $params = [])
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
     */
    public function errorPage($message = '', $exception = null, $viewPath = null)
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
        $this->displayPage($viewPath, ['message' => $message, "exception" => $exception]);
    }
}