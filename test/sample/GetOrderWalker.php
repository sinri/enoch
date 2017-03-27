<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 10:45
 */

namespace sinri\enoch\test\sample;


use sinri\enoch\core\Spirit;
use sinri\enoch\core\Walker;

class GetOrderWalker extends Walker
{

    /**
     * Set the value $this->db, and return it.
     * Return false for not using database
     * @return mixed
     */
    public function installDatabase()
    {
        // TODO: Implement installDatabase() method.
        return false;
    }

    /**
     * Set the value $this->sftp, and return it.
     * Return false for not using sftp
     * @return mixed
     */
    public function installSFTP()
    {
        // TODO: Implement installSFTP() method.
        return false;
    }

    public function walk()
    {
        // TODO: Implement walk() method.
        $this->logger->log(Spirit::LOG_INFO,__METHOD__."@".__LINE__);
        return true;
    }
}