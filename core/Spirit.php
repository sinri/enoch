<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 11:02
 */

namespace sinri\enoch\core;


class Spirit
{
    const LOG_INFO = 'INFO';
    const LOG_WARNING = 'WARNING';
    const LOG_ERROR = 'ERROR';

    protected static $instance = null;
    protected static $useColoredTerminalOutput = false;

    /**
     * @return bool
     */
    public static function isUseColoredTerminalOutput()
    {
        return self::$useColoredTerminalOutput;
    }

    /**
     * @param bool $useColoredTerminalOutput
     */
    public static function setUseColoredTerminalOutput($useColoredTerminalOutput)
    {
        self::$useColoredTerminalOutput = $useColoredTerminalOutput;
    }

    protected function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Spirit();
        }
        return self::$instance;
    }

    final public function generateLog($level, $message, $object = '')
    {
        $lcc = new LibConsoleColor();

        $now = date('Y-m-d H:i:s');

        if (self::$useColoredTerminalOutput) {
            if ($level === Spirit::LOG_ERROR) {
                $level_string = $lcc->getColorWord("[{$level}]", LibConsoleColor::Red);
            } elseif ($level === Spirit::LOG_WARNING) {
                $level_string = $lcc->getColorWord("[{$level}]", LibConsoleColor::Yellow);
            } else {
                $level_string = $lcc->getColorWord("[{$level}]", LibConsoleColor::Green);
            }
        } else {
            $level_string = "[{$level}]";
        }

        $log = "{$now} {$level_string} {$message} |";
        if (!is_string($object)) {
            $log .= json_encode($object, JSON_UNESCAPED_UNICODE);
        } else {
            $log .= $object;
        }
        $log .= PHP_EOL;

        return $log;
    }

    /**
     * This could be overrode for customized log output
     * @param $level
     * @param $message
     * @param string $object
     */
    public function log($level, $message, $object = '')
    {
        echo $this->generateLog($level, $message, $object);
    }

    const REQUEST_NO_ERROR = 0;
    const REQUEST_FIELD_NOT_FOUND = 1;
    const REQUEST_REGEX_NOT_MATCH = 2;

    public function getRequest($name, $default = null, $regex = null, &$error = 0)
    {
        $error = self::REQUEST_NO_ERROR;
        if (!isset($_REQUEST[$name])) {
            $error = self::REQUEST_FIELD_NOT_FOUND;
            return $default;
        }
        $value = $_REQUEST[$name];
        if ($regex === null) {
            return $value;
        }
        if (!preg_match($regex, $value)) {
            $error = self::REQUEST_REGEX_NOT_MATCH;
            return $default;
        }
        return $value;
    }

    public function readGet($name, $default = null, $regex = null, &$error = 0)
    {
        if (!isset($_GET[$name])) {
            $error = self::REQUEST_FIELD_NOT_FOUND;
            return $default;
        }
        $value = $_GET[$name];
        if ($regex === null) {
            return $value;
        }
        if (!preg_match($regex, $value)) {
            $error = self::REQUEST_REGEX_NOT_MATCH;
            return $default;
        }
        return $value;
    }

    public function readPost($name, $default = null, $regex = null, &$error = 0)
    {
        if (!isset($_POST[$name])) {
            $error = self::REQUEST_FIELD_NOT_FOUND;
            return $default;
        }
        $value = $_POST[$name];
        if ($regex === null) {
            return $value;
        }
        if (!preg_match($regex, $value)) {
            $error = self::REQUEST_REGEX_NOT_MATCH;
            return $default;
        }
        return $value;
    }

    public function fullPostFields()
    {
        return $_POST ? $_POST : [];
    }

    public function fullGetFields()
    {
        return $_GET ? $_GET : [];
    }

    /**
     * 是否是AJAx提交的
     */
    public function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否是GET提交的
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是POST提交
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
    }

    public function isCLI()
    {
        return (php_sapi_name() === 'cli') ? true : false;
    }

    const AJAX_JSON_CODE_OK = "OK";
    const AJAX_JSON_CODE_FAIL = "FAIL";

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

    public function displayPage($filepath, $params = [])
    {
        extract($params);
        require $filepath;
    }

    public function errorPage($message = '', $exception = null)
    {
        // TODO: beautify it.
        echo "<pre>" . PHP_EOL;
        echo $message;
        echo PHP_EOL;
        if ($exception) {
            var_dump($exception);
        }
    }
}
