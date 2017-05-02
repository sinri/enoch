<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/2
 * Time: 10:17
 */

namespace sinri\enoch\test\queue;


use sinri\enoch\service\QueueInterface;
use sinri\enoch\service\QueueItem;

class FileQueue implements QueueInterface
{

    private $dir_path;

    public function __construct($path)
    {
        $this->dir_path = $path;
    }

    /**
     * @param $object QueueItem
     * @return mixed QueueItemIndex on success or false when error occurs
     */
    public function addToQueueTail($object)
    {
        $new_index = 10000000000;
        $list = $this->findList();
        if (!empty($list)) {
            $tail = $list[count($list) - 1];
            $old_index = $this->parseIndex($tail);
            if (!$old_index) return false;
            $new_index = $old_index + 1;
        }

        $r = file_put_contents($this->dir_path . '/' . $new_index . '.job', json_encode($object->getQueueItemData()));
        return $r ? true : false;
    }

    /**
     * @return QueueItem|bool QueueItem on success or false when error occurs
     */
    public function takeFromQueueHead()
    {
        $list = $this->findList();
        if (empty($list)) return false;
        $item_path = $list[0];
        $index = $this->parseIndex($item_path);
        $json = file_get_contents($item_path);
        $data = json_decode($json, true);

        $item = new FileQueueItem($data);
        $item->setQueueItemIndex($index);

        // Here might be some codes to do LOCK

        return $item;
    }

    protected function findList()
    {
        $list = glob($this->dir_path . '/*.job');
        return $list;
    }

    protected function parseIndex($path)
    {
        if (preg_match('/(\d+)\.job$/', $path, $matches)) {
            $old_index = $matches[1];
            return $old_index;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function removeQueueItem($index = null)
    {
        if (!$index) {
            $item = $this->takeFromQueueHead();
            if (!$item) return false;
            $index = $item->getQueueItemIndex();
        }
        //rename($this->dir_path.'/'.$index.'.job',$this->dir_path.'/'.$index.'.done');
        return unlink($this->dir_path . '/' . $index . '.job');
    }
}