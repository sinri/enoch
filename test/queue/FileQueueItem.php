<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/2
 * Time: 10:18
 */

namespace sinri\enoch\test\queue;


use sinri\enoch\service\QueueItem;

class FileQueueItem extends QueueItem
{

    /**
     * @return bool
     */
    public function handle()
    {
        $index = $this->getQueueItemIndex();
        if (!$index) {
            return false;
        }
        $data = $this->getQueueItemData();
        if (!isset($data['value'])) {
            $data['value'] = '__EMPTY__';
        }
        echo "[$index] {$data['value']}" . PHP_EOL;
        return true;
    }
}