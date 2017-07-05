<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/4
 * Time: 23:36
 */

namespace sinri\enoch\core;

use sinri\enoch\helper\CommonHelper;
use sinri\enoch\mvc\BaseCodedException;

/**
 * Class Enos
 * CronJob Worker. You may refer to EnosCalls or EnosCallsByArgv to build runner.
 * And to Seth, to him also there was born a son; and he called his name Enos: then began men to call upon the name of the LORD.
 * (Genesis 4:26)
 * @since 1.5.3
 * @package sinri\enoch\core
 */
abstract class Enos
{
    protected $logger;
    protected $config;
    protected $helper;

    public function __construct()
    {
        $this->logger = $this->loadLogger();
        $this->config = $this->readConfig();
        $this->helper = new CommonHelper();
    }

    /**
     * @return LibLog
     */
    protected function loadLogger()
    {
        return new LibLog();
    }

    /**
     * If keep $keyChain as null, return the whole config array;
     * If give $keyChain as array, it should work to return as
     * `$this->helper->safeReadNDArray($config,$keyChain,$default);`.
     * @param null|array $keyChain
     * @param null|mixed $default
     * @return array
     */
    abstract protected function readConfig($keyChain = null, $default = null);

    /**
     * @param array $names such as [ACTION_NAME=>PARAM_ARRAY,...]
     * @throws BaseCodedException
     */
    public function call($names = [])
    {
        foreach ($names as $name => $params) {
            $method = "action" . $name;
            if (!method_exists($this, $method)) {
                throw new BaseCodedException("No such action: " . $method, BaseCodedException::NOT_IMPLEMENT_ERROR);
            }
            call_user_func_array([$this, $method], $params);
        }
    }

    /**
     * @throws BaseCodedException
     */
    public function actionDefault()
    {
        throw new BaseCodedException("Not defined yet.", BaseCodedException::NOT_IMPLEMENT_ERROR);
    }
}