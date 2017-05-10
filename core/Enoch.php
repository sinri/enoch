<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 09:51
 */

namespace sinri\enoch\core;

abstract class Enoch
{
    protected $logger=null;
    protected $projectName="";
    protected $projectBase=".";
    protected $config=[];

    public function __construct()
    {
        $this->logger = new LibLog();
    }

    public function initialize($projectName, $projectBase)
    {
        $this->projectName = $projectName;
        $this->projectBase = $projectBase;

        $this->readConfig();
    }

    /**
     * Fulfill the config property.
     * By default, read local file: projectBase/config.php;
     * Recommended, read database (to be overrode)
     */
    public function readConfig()
    {
        // by default, read local file: projectBase/config.php
        // in which only an array $config defined
        $config=[];
        if (file_exists($this->projectBase.'/config.php')) {
            require($this->projectBase . '/config.php');
        }
        $this->config=$config;
    }

    public function start()
    {
        if (!isset($this->config['walkers']) || empty($this->config['walkers'])) {
            $this->logger->log(LibLog::LOG_ERROR, "There is not an available walker configured.");
            return false;
        }
        foreach ($this->config['walkers'] as $walker_name => $status) {
            if ($status) {
                $goNext=$this->walkWith($walker_name);
                if (!$goNext) {
                    $this->logger->log(LibLog::LOG_WARNING, "The walker '{$walker_name}' stopped walking, exit.");
                    return false;
                }
            }
        }
        $this->logger->log(LibLog::LOG_INFO, "All walkers have satisfied.");
        return true;
    }

    public function walkWith($walkerName)
    {
        $class_file = $this->projectBase . '/' . $walkerName . 'Walker.php';
        if (!file_exists($class_file)) {
            $this->logger->log(LibLog::LOG_ERROR, "No such walker!");
            return false;
        }
        require_once $class_file;
        $walker = $this->getWalkerInstance($walkerName);
        if (!is_a($walker, 'sinri\enoch\core\Walker')) {
            $this->logger->log(LibLog::LOG_ERROR, "The walker is not of sinri\\enoch\\core\\Walker");
            return false;
        }
        try {
            $goNext = call_user_func_array([$walker, 'walk'], []);
            return $goNext;
        } catch (\Exception $exception) {
            $this->logger->log(LibLog::LOG_ERROR, "Walk into a trap: " . $exception->getMessage());
            return false;
        }
    }

    abstract protected function getWalkerInstance($walkerName);
}
