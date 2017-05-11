<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/2
 * Time: 10:09
 */

namespace sinri\enoch\service;


interface QueueInterface
{
    /**
     * @param $object QueueItem
     * @return mixed QueueItemIndex on success or false when error occurs
     */
    public function addToQueueTail($object);

    /**
     * @return QueueItem|bool QueueItem on success or false when error occurs
     */
    public function takeFromQueueHead();

    /**
     * @param null|int $index
     * @return bool
     */
    public function removeQueueItem($index = null);
}