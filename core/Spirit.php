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

    protected static $instance=null;

    protected function __construct()
    {

    }

    public static function getInstance(){
        if(!self::$instance){
            self::$instance=new Spirit();
        }
        return self::$instance;
    }

    public final function generateLog($level,$message,$object=''){
        $now=date('Y-m-d H:i:s');

        $log = "{$now} [{$level}] {$message} |";
        if(!is_string($object)) {
            $log .= json_encode($object, JSON_UNESCAPED_UNICODE);
        }elsE{
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
    public function log($level,$message,$object=''){
        echo $this->generateLog($level,$message,$object);
    }
}