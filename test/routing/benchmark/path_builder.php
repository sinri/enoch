<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/22
 * Time: 11:15
 */

$paths = [
    "",
];
$cases = [
    "/",
];

// dynamic generated path

for ($counting = 0; $counting < 10000 - 1; $counting++) {
    $path_length = rand(1, 10);
    $path = "";
    $case = "/";
    $case_over = false;
    for ($i = 0; $i < $path_length; $i++) {
        $component = "";
        if ($i > 0) {
            $path .= "/";
            if (!$case_over) $case .= "/";
        }
        $component = rand(1, 10000);
        $flag = rand(1, 10);
        if (!$case_over) $case .= $component;
        if ($flag > 8) {
            $component = '{' . $component . '?}';
            if ($flag > 9) {
                $case_over = true;
            }
        } elseif ($flag > 5) {
            $component = '{' . $component . '}';
        }
        $path .= $component;
    }
    $paths[] = $path;
    $cases[] = $case;
}

file_put_contents(__DIR__ . '/paths.json', json_encode(["paths" => $paths, "cases" => $cases]));