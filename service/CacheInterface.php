<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/2
 * Time: 11:54
 */

namespace sinri\enoch\service;


interface CacheInterface
{
    /**
     * @param string $key
     * @param mixed $object
     * @param int $life 0 for no limit, or seconds
     * @return bool
     */
    public function saveObject($key, $object, $life = 0);

    /**
     * @param string $key
     * @return mixed|bool
     */
    public function getObject($key);

    /**
     * @param string $key
     * @return bool
     */
    public function removeObject($key);

    /**
     * @return bool
     */
    public function removeExpiredObjects();
}