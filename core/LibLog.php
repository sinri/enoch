<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 16:26
 */

namespace sinri\enoch\core;

use sinri\enoch\helper\CommonHelper;


/**
 * Class LibLog
 * @since 1.2.9
 * @package sinri\enoch\core
 */
class LibLog
{
    const LOG_DEBUG = "DEBUG";
    const LOG_INFO = 'INFO';
    const LOG_WARNING = 'WARNING';
    const LOG_ERROR = 'ERROR';

    protected $useColoredTerminalOutput = false;

    protected $targetLogDir = null;
    protected $prefix = 'enoch';

    protected $ignoreLevel;

    protected $forceUseStandardOutputInCLI = true;

    function __construct($targetLogDir = null, $prefix = '')
    {
        $this->targetLogDir = $targetLogDir;
        $this->prefix = $prefix;
        $this->ignoreLevel = self::LOG_DEBUG;
        $this->forceUseStandardOutputInCLI = true;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param null $targetLogDir
     */
    public function setTargetLogDir($targetLogDir)
    {
        $this->targetLogDir = $targetLogDir;
    }

    /**
     * @param string $ignoreLevel
     */
    public function setIgnoreLevel($ignoreLevel)
    {
        $this->ignoreLevel = $ignoreLevel;
    }

    /**
     * @return bool
     */
    public function isUseColoredTerminalOutput()
    {
        return $this->useColoredTerminalOutput;
    }

    /**
     * @param bool $useColoredTerminalOutput
     */
    public function setUseColoredTerminalOutput($useColoredTerminalOutput)
    {
        $this->useColoredTerminalOutput = $useColoredTerminalOutput;
    }

    /**
     * @param $level
     * @return bool
     */
    protected function shouldIgnoreThisLog($level)
    {
        static $levelValue = [
            self::LOG_DEBUG => 0,
            self::LOG_INFO => 1,
            self::LOG_WARNING => 2,
            self::LOG_ERROR => 3,
        ];
        $coming = CommonHelper::safeReadArray($levelValue, $level, 4);
        $limit = CommonHelper::safeReadArray($levelValue, $this->ignoreLevel, 0);
        if ($coming < $limit) {
            return true;
        }
        return false;
    }

    /**
     * This could be overrode for customized log output
     * @param $level
     * @param $message
     * @param string $object
     */
    public function log($level, $message, $object = '')
    {
        if ($this->shouldIgnoreThisLog($level)) {
            return;
        }
        $msg = $this->generateLog($level, $message, $object);
        $target_file = $this->decideTargetFile();
        if (!$target_file) {
            echo $msg;
            return;
        }
        @file_put_contents($target_file, $msg, FILE_APPEND);
    }

    /**
     * Return the string format log content
     * @param $level
     * @param $message
     * @param string $object
     * @return string
     */
    public function generateLog($level, $message, $object = '')
    {
        $now = date('Y-m-d H:i:s');
        $level_string = "[{$level}]";
        if ($this->useColoredTerminalOutput) {
            $lcc = new LibConsoleColor();
            switch ($level) {
                case self::LOG_DEBUG:
                    $level_string = $lcc->getColorWord("[{$level}]", LibConsoleColor::Blue);
                    break;
                case self::LOG_ERROR:
                    $level_string = $lcc->getColorWord("[{$level}]", LibConsoleColor::Red);
                    break;
                case self::LOG_WARNING:
                    $level_string = $lcc->getColorWord("[{$level}]", LibConsoleColor::Yellow);
                    break;
                default:
                    $level_string = $lcc->getColorWord("[{$level}]", LibConsoleColor::Green);
                    break;
            }
        }

        $log = "{$now} {$level_string} {$message} |";
        $log .= is_string($object) ? $object : json_encode($object, JSON_UNESCAPED_UNICODE);
        $log .= PHP_EOL;

        return $log;
    }

    /**
     * Return the target file path which log would be written into.
     * If target log directory not set, return false.
     * @return bool|string
     */
    public function decideTargetFile()
    {
        if (empty($this->targetLogDir)) {
            return false;
        }
        if ($this->forceUseStandardOutputInCLI && LibRequest::isCLI()) {
            return false;
        }
        if (!file_exists($this->targetLogDir)) {
            @mkdir($this->targetLogDir, 0777, true);
        }
        $today = date('Y-m-d');
        return $this->targetLogDir . '/log-' . (empty($this->prefix) ? '' : $this->prefix . '-') . $today . '.log';
    }
}