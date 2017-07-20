<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/20
 * Time: 14:01
 */

namespace sinri\enoch\test\queue;


use sinri\enoch\service\RedisQueueItem;

class SimpleRedisQueueItem extends RedisQueueItem
{
    public function handle()
    {
        print_r($this->queueItemData);
        return true;
    }
}