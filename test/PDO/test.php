<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/8/25
 * Time: 16:57
 */
require_once __DIR__ . '/../../autoload.php';


$sql_template = /** @lang sql-template */
    'select key_field,value,`?`,`status`
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


$sql_template = $sql = 'SELECT 
            `tachiba_department_user`.`core_user_id` AS `core_user_id`,
            concat( ? ,`tachiba_department_user`.`department_id`) AS `tag` 
        FROM `tachiba_department_user` 
        WHERE `tachiba_department_user`.`status` = \'NORMAL\'
	    ';
$params = [
    "DEPARTMENT_",
];
$sql = (new \sinri\enoch\core\LibPDO())->safeBuildSQL($sql_template, [100]);
echo "FIN: " . PHP_EOL . $sql . PHP_EOL;
