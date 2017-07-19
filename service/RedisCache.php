<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/19
 * Time: 17:15
 */

namespace sinri\enoch\service;


use Predis\Client;

class RedisCache implements CacheInterface
{
    protected $client = null;

    public function __construct($host, $port = 6379, $database = 255, $password = null)
    {
        $single_server = array(
            'host' => $host,
            'port' => $port,
            'database' => $database,
        );
        if ($password) $single_server['password'] = $password;
        $this->client = new Client($single_server);
    }

    /**
     * @param string $key
     * @param mixed $object
     * @param int $life 0 for no limit, or seconds
     * @return bool
     */
    public function saveObject($key, $object, $life = 0)
    {
        if ($life > 0) {
            return $this->client->setex($key, $life, $object);
        }
        return $this->client->set($key, $object);
    }

    /**
     * @param string $key
     * @return mixed|bool
     */
    public function getObject($key)
    {
        return $this->client->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function removeObject($key)
    {
        return $this->client->del([$key]);
    }

    /**
     * @return bool
     */
    public function removeExpiredObjects()
    {
        // REDIS WOULD DO THIS...
        return true;
    }
}