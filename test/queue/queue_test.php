<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/2
 * Time: 10:34
 */

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/FileQueue.php';
require_once __DIR__ . '/FileQueueItem.php';

$queue = new \sinri\enoch\test\queue\FileQueue(__DIR__ . '/queue_item_dir');
for ($i = 0; $i < 40; $i++) {
    $item = new \sinri\enoch\test\queue\FileQueueItem(['value' => $i]);
    $enqueued = $queue->addToQueueTail($item);
    //echo "ENQUEUE [$i]: ".json_encode($enqueued).PHP_EOL;

    if (rand(1, 10) > 5) {
        $head = $queue->takeFromQueueHead();
        if ($head) {
            //echo "TOKEN HEAD".PHP_EOL;
            $done = $head->handle();
            if ($done) {
                $index = $head->getQueueItemIndex();
                $removed = $queue->removeQueueItem($index);
                echo "DEQUEUED [{$index}]" . PHP_EOL;
            }
        }
    }
}