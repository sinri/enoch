<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 10:17
 */

namespace sinri\enoch\core;


abstract class Walker
{
    protected $logger=null;
    protected $config=[];
    protected $db=null;
    protected $sftp=null;

    public function __construct($config)
    {
        $this->config=$config;
        $this->logger=Spirit::getInstance();
        $this->initialize();
    }

    public function initialize(){
        //initialize $db
        $this->installDatabase();
        //initialize $sftp
        $this->installSFTP();
    }

    /**
     * Set the value $this->db, and return it: $this->sftp=new LibSFTP([...]);
     * Return false for not using database
     * @return mixed
     */
    abstract public function installDatabase();
    /**
     * Set the value $this->sftp, and return it: $this->>db=new LibMySQL([...]);
     * Return false for not using sftp
     * @return mixed
     */
    abstract public function installSFTP();

    abstract public function walk();
}