<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/20
 * Time: 12:02
 */

namespace sinri\enoch\service;


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
     */
    public function handle()
    {
        // TODO: Implement handle() method.
        print_r($this->queueItemData);
        return true;
    }
}