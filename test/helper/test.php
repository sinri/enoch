<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/22
 * Time: 09:35
 */

require_once __DIR__ . '/../../autoload.php';

$helper = new \sinri\enoch\helper\CommonHelper();

echo \sinri\enoch\helper\CommonHelper::safeReadArray(["asd" => "123"], "asd");// this is correct for ^2.0
echo $helper->safeReadArray(["asd" => "123"], "asdd", '333'); // this is for ^1.0 but also available in ^2.0

// SAFE ARRAY READER

$array = [
    "Ein" => 1,
    "Zwie" => ["Ni" => 2],
    "A" => ["B" => ["C" => "D"]],
];

$result = $helper->safeReadArray($array, "Ein", 100, null, $error);
if (!($result === 1 && $error === \sinri\enoch\helper\CommonHelper::REQUEST_NO_ERROR)) {
//    throw new \sinri\enoch\mvc\BaseCodedException("ERROR");
    echo __LINE__ . PHP_EOL;
}

$result = $helper->safeReadNDArray($array, ["A", "B", "C"], "E", null, $error);
if (!($result === "D" && $error === \sinri\enoch\helper\CommonHelper::REQUEST_NO_ERROR)) {
//    throw new \sinri\enoch\mvc\BaseCodedException("ERROR");
    echo __LINE__ . PHP_EOL;
}


$result = $helper->safeReadNDArray($array, ["A", "B", "C"], "E", '/^F$/', $error);
if (!($result === "D" && $error === \sinri\enoch\helper\CommonHelper::REQUEST_NO_ERROR)) {
    var_dump($error);
//    throw new \sinri\enoch\mvc\BaseCodedException("ERROR");
    echo __LINE__ . PHP_EOL;
}

\sinri\enoch\helper\CommonHelper::safeWriteNDArray($array, ['a', 'b', 'c'], 1);
var_dump($array);