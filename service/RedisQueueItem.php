<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/20
 * Time: 12:02
 */

namespace sinri\enoch\service;


use sinri\enoch\mvc\BaseCodedException;

class RedisQueueItem extends QueueItem
{
    /**
     * @return string
     */
    public function stringify()
    {
        return json_encode($this->queueItemData);
    }

    /**
     * @param $string
     * @return RedisQueueItem
     */
    public static function newItemFromDataString($string)
    {
        $data = @json_encode($string, true);
        return new RedisQueueItem($data);
    }

    /**
     * @return bool
     * @throws BaseCodedException
     */
    public function handle()
    {
        throw new BaseCodedException("Not implement", BaseCodedException::NOT_IMPLEMENT_ERROR);
    }
}