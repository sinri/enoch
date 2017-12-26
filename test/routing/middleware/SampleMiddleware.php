<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 10:55
 */

namespace sinri\enoch\test\routing\middleware;


use sinri\enoch\helper\CommonHelper;
use sinri\enoch\mvc\MiddlewareInterface;

class SampleMiddleware extends MiddlewareInterface
{
    public function shouldAcceptRequest($path, $method, $params, &$preparedData = null, &$responseCode = 200, &$error = null)
    {
        //print_r(["PATH"=>$path,"METHOD"=>$method,"PARAMS"=>$params]);
        $sample_issue_value = CommonHelper::safeReadArray($params, 1, -1);
        if ($sample_issue_value == 100) {
            return false;
        } elseif ($sample_issue_value == 50) {
            $preparedData = 50;
        } else {
            $preparedData = 10;
        }
        return true;
    }
}