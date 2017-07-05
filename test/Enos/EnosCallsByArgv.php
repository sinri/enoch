<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/5
 * Time: 10:16
 */

require_once __DIR__ . '/../../autoload.php';

/**
 * # run defaultCall
 * php EnosCallsByArgv.php SampleEnos
 * # run action
 * php EnosCallsByArgv.php SampleEnos StepA
 * php EnosCallsByArgv.php SampleEnos StepB 3 4
 */

$helper = new \sinri\enoch\helper\CommonHelper();

$enos_instance_name = $helper->safeReadArray($argv, 1);

if (empty($enos_instance_name)) {
    echo "Enos Instance Unknown" . PHP_EOL;
    exit();
}

$enos_instance_full_name = '\\sinri\\enoch\\test\\Enos\\' . $enos_instance_name;
$enos = new $enos_instance_full_name();

$action = $helper->safeReadArray($argv, 2, 'Default');

$params = [];
for ($i = 3; $i < $argc; $i++) {
    $params[] = $argv[$i];
}

call_user_func_array([$enos, 'action' . $action], $params);