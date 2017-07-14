<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 13:59
 */

namespace sinri\enoch\core;

/**
 * Class LibMySQL
 * @package sinri\enoch\core
 * @since ^2.0 it is just a shell of LibPDO
 * @deprecated
 */
class LibMySQL extends LibPDO
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * LibMySQL constructor.
     * @param null|array $params
     */
    public function __construct($params = null)
    {
        parent::__construct($params);
    }
}
