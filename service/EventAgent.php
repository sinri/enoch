<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/14
 * Time: 17:42
 */

namespace sinri\enoch\service;


class EventAgent
{
    /**
     * @var array of EventListener
     */
    protected $eventListenerList;

    public function __construct()
    {
        $this->eventListenerList = [];
    }

    /**
     * @param EventListener $eventListener
     */
    public function registerEventListener($eventListener)
    {
        $this->eventListenerList[$eventListener->getEventName()] = $eventListener;
    }

    /**
     * @param $eventName
     * @param array $params
     */
    public function fire($eventName, $params = [])
    {
        $event = $this->getRegisteredEventListener($eventName);
        if ($eventName) $event->happen($params);
    }

    /**
     * @param $eventName
     * @return EventListener|bool
     */
    protected function getRegisteredEventListener($eventName)
    {
        if (isset($this->eventListenerList[$eventName])) {
            return $this->eventListenerList[$eventName];
        }
        return false;
    }
}