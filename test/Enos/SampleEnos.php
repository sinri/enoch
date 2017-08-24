<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/5
 * Time: 09:17
 */

namespace sinri\enoch\test\Enos;


use sinri\enoch\core\Enos;
use sinri\enoch\core\LibLog;
use sinri\enoch\helper\CommonHelper;

class SampleEnos extends Enos
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return LibLog
     */
    protected function loadLogger()
    {
        // Implement loadLogger() method.
        // If you run this under CLI, no target file decided, use echo;
        // If you still want to write log into file directly, define a correct LigLog instance.
        return new LibLog();
    }

    protected function loadConfig()
    {
        return $this->readConfig(['actions']);
    }

    /**
     * @param null|array $keyChain
     * @param null|mixed $default
     * @return array|mixed
     */
    public function readConfig($keyChain = null, $default = null)
    {
        //Implement readConfig() method.
        //The $config might be read from file/database, as you like.
        $config = [
            "actions" => [
                "StepA" => [],
                "StepB" => [1],
            ],
        ];
        if ($keyChain === null) {
            return $config;
        }
        return CommonHelper::safeReadNDArray($config, $keyChain, $default);
    }

    public function actionStepA()
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function actionStepB($p1, $p2 = 3)
    {
        echo __METHOD__ . "(" . $p1 . "," . $p2 . ")=" . $this->innerProcessForB($p1, $p2) . PHP_EOL;
    }

    protected function innerProcessForB($p1, $p2)
    {
        return $p1 + $p2;
    }

    public function actionDefault()
    {
        echo __METHOD__ . PHP_EOL;
    }
}