<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/2
 * Time: 10:12
 */

namespace sinri\enoch\service;


abstract class QueueItem
{
    protected $queueItemIndex;
    protected $queueItemData;

    public function __construct($data)
    {
        $this->queueItemIndex = null;
        $this->queueItemData = $data;
    }

    /**
     * @return mixed
     */
    public function getQueueItemIndex()
    {
        return $this->queueItemIndex;
    }

    /**
     * @param mixed $queueItemIndex
     */
    public function setQueueItemIndex($queueItemIndex)
    {
        $this->queueItemIndex = $queueItemIndex;
    }

    /**
     * @return mixed
     */
    public function getQueueItemData()
    {
        return $this->queueItemData;
    }

    /**
     * @param mixed $queueItemData
     */
    public function setQueueItemData($queueItemData)
    {
        $this->queueItemData = $queueItemData;
    }

    /**
     * @return bool
     */
    abstract public function handle();
}