<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/5
 * Time: 09:21
 */

require_once __DIR__ . '/../../autoload.php';

/**
 * # call defaultCall
 * php EnosCalls.php -e SampleEnos
 * # call configured actions
 * php EnosCalls.php -e SampleEnos -c actions
 * # call single action
 * php EnosCalls.php -e SampleEnos -a StepB -p"1,3"
 *
 * You can also define your own caller with $argv.
 */

$options = getopt("e:c:a:p::");
//var_dump($options);

if (!isset($options['e'])) {
    echo "No Enos Instance Determined" . PHP_EOL;
    exit();
}

$enos_instance_name = $options['e'];
$enos_instance_full_name = '\\sinri\\enoch\\test\\Enos\\' . $enos_instance_name;
$enos = new $enos_instance_full_name();

if (isset($options['c'])) {
    $keyChain = explode(',', $options['c']);
    $params = call_user_func_array([$enos, 'readConfig'], $keyChain);
    call_user_func_array([$enos, 'call'], [$params]);
} elseif (isset($options['a'])) {
    $params = [];
    if (isset($options['p'])) {
        $params = explode(",", $options['p']);
    }
    call_user_func_array([$enos, "action" . $options['a']], $params);
} else {
    call_user_func_array([$enos, 'actionDefault'], []);
}
