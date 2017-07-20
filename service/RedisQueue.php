<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/20
 * Time: 11:30
 */

namespace sinri\enoch\service;


use Predis\Client;
use sinri\enoch\mvc\BaseCodedException;

/**
 * Class RedisQueue
 * @package sinri\enoch\service
 */
class RedisQueue implements QueueInterface
{
    protected $listKey = null;
    protected $client = null;

    public function __construct($list_key, $host, $port = 6379, $database = 255, $password = null)
    {
        $this->listKey = $list_key;
        $single_server = array(
            'host' => $host,
            'port' => $port,
            'database' => $database,
        );
        if ($password) $single_server['password'] = $password;
        $this->client = new Client($single_server);
    }

    /**
     * @param $object RedisQueueItem
     * @return mixed QueueItemIndex on success or false when error occurs
     */
    public function addToQueueTail($object)
    {
        return $this->client->lpush($this->listKey, [$object->stringify()]);
    }

    /**
     * @return RedisQueueItem|bool QueueItem on success or false when error occurs
     */
    public function takeFromQueueHead()
    {
        //移除并获取列表最后一个元素
        $string = $this->client->rpop($this->listKey);
        return RedisQueueItem::newItemFromDataString($string);
    }

    /**
     * @param null|int $index
     * @return bool
     * @throws BaseCodedException
     */
    public function removeQueueItem($index = null)
    {
        throw new BaseCodedException("This is not available with Redis.", BaseCodedException::NOT_IMPLEMENT_ERROR);
    }

    /**
     * @return int
     */
    public function queueLength()
    {
        return $this->client->llen($this->client);
    }

    /**
     * @param $index
     * @return RedisQueueItem
     */
    public function objectAtIndex($index)
    {
        $string = $this->client->lindex($this->listKey, $index);
        return RedisQueueItem::newItemFromDataString($string);
    }
}