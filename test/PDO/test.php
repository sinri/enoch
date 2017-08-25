<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/8/25
 * Time: 16:57
 */
require_once __DIR__ . '/../../autoload.php';


$sql_template = /** @lang sql-template */
    'select key_field,value,`?`
         from `?`.`?`
         where key_field in (?)
         and status = ?
         and rate>{?}
         limit [?] , [?]';

$sql = (new \sinri\enoch\core\LibPDO())->safeBuildSQL($sql_template, [
    'rate', 'scheme', 'table', ['hang"zhou', 'shang"hai'], 'normal', 0.8, 40, 1
]);
echo "FIN: " . PHP_EOL . $sql . PHP_EOL;
//FIN:
//select key_field,value,`rate`
//         from `scheme`.`table`
//         where key_field in ('hang\"zhou','shang\"hai')
//and status = 'normal'
//and rate>0.8
//         limit 40 , 1
