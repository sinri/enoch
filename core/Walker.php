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
    protected $mailer = null;

    public function __construct($config)
    {
        $this->config=$config;
        $this->logger = new Spirit();
        $this->initialize();
    }

    public function initialize()
    {
        $this->installDatabase();
        $this->installSFTP();
        $this->installMailer();
    }

    /**
     * Set the value $this->db, and return it: $this->sftp=new LibSFTP([...]);
     * Return false for not using database
     * @return mixed
     */
    public function installDatabase()
    {
        return false;
    }
    /**
     * Set the value $this->sftp, and return it: $this->db=new LibMySQL([...]);
     * Return false for not using sftp
     * @return mixed
     */
    public function installSFTP()
    {
        return false;
    }

    /**
     * Set the value $this->mailer, and return it: $this->mailer=new LibMail([...]);
     * Return false for not using mail
     * @return mixed
     */
    public function installMailer()
    {
        return false;
    }

    abstract public function walk();
}
