<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 10:07
 */

// the sample config.php file for project sample
// non-optional fields:
// walkers
// optional fields:
// mail_list
// others: ...

$config = [
    // walkers contain: WALKER_NAME => BOOLEAN
    'walkers' => [
        "GetOrder" => true,
        "SendOrder" => true,
    ],
    "mail_list" => [
        "tester"=>"erp@leqee.com",
    ],
];