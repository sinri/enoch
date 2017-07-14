<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 23:16
 */

namespace sinri\enoch\mvc;

class BaseCodedException extends \Exception
{
    const DEFAULT_ERROR = 0;
    const NOT_IMPLEMENT_ERROR = 1;
    const DEPRECATED_REMOVED = 2;
    const ASSERT_FAILED = 3;

    const USER_NOT_LOGIN = 101;
    const USER_NOT_ADMIN = 102;
    const USER_NOT_PRIVILEGED = 103;

    const ACT_NOT_EXISTS = 200;
    const METHOD_NOT_EXISTS = 204;
    const NO_MATCHED_ROUTE = 210;
    const VIEW_NOT_EXISTS = 211;
    const ACTION_NO_HANDLER = 220;
    const RESOURCE_NOT_EXISTS = 221;

    const REQUEST_FILTER_REJECT = 403;
}