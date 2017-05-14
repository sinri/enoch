<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/14
 * Time: 17:20
 */

namespace sinri\enoch\service;


class EventListener
{
    protected $eventName;

    /**
     * @return null
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    protected $handler;

    /**
     * EventListener constructor.
     * @param null $eventName
     * @param null $handler
     */
    public function __construct($eventName = null, $handler = null)
    {
        $this->eventName = $eventName;
        $this->handler = $handler;
    }

    /**
     * @param null $eventName
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @param null $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param array $params
     */
    public function happen($params = [])
    {
        call_user_func_array($this->handler, [$params]);
    }
}