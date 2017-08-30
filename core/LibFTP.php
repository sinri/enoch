<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/8/18
 * Time: 11:47
 */

namespace sinri\enoch\core;


use sinri\enoch\mvc\BaseCodedException;

class LibFTP
{
    protected $ftpServer;
    protected $ftpPort;
    protected $ftpConnectTimeout;
    protected $ftpUsername;
    protected $ftpPassword;
    protected $ftpMode;
    protected $ftpUsePassive;

    public function __construct()
    {
        $this->ftpConnectTimeout = 30;
        $this->ftpMode = FTP_BINARY;
        $this->ftpUsePassive = true;
    }

    /**
     * @param bool $ftpUsePassive
     */
    public function setFtpUsePassive($ftpUsePassive)
    {
        $this->ftpUsePassive = $ftpUsePassive;
    }

    /**
     * @param mixed $ftpServer
     */
    public function setFtpServer($ftpServer)
    {
        $this->ftpServer = $ftpServer;
    }

    /**
     * @param mixed $ftpPort
     */
    public function setFtpPort($ftpPort)
    {
        $this->ftpPort = $ftpPort;
    }

    /**
     * @param mixed $ftpConnectTimeout
     */
    public function setFtpConnectTimeout($ftpConnectTimeout)
    {
        $this->ftpConnectTimeout = $ftpConnectTimeout;
    }

    /**
     * @param mixed $ftpUsername
     */
    public function setFtpUsername($ftpUsername)
    {
        $this->ftpUsername = $ftpUsername;
    }

    /**
     * @param mixed $ftpPassword
     */
    public function setFtpPassword($ftpPassword)
    {
        $this->ftpPassword = $ftpPassword;
    }

    /**
     * @param int $ftpMode Either `FTP_ASCII` or `FTP_BINARY`.
     * @throws BaseCodedException
     */
    public function setFtpMode($ftpMode)
    {
        if (!in_array($ftpMode, [FTP_ASCII, FTP_BINARY])) {
            throw new BaseCodedException("Unknown FTP Mode");
        }
        $this->ftpMode = $ftpMode;
    }

    /**
     * @param string $filename
     * @param string $remotePath
     * @param bool $useSSL
     * @return bool
     */
    public function sendFileToFTP($filename, $remotePath, $useSSL = false)
    {
        $mode = $this->ftpMode;
        return $this->handleRequest(function ($connection, &$error = null) use ($filename, $remotePath, $mode) {
            $result = ftp_put($connection, $remotePath, $filename, $mode);
            if (!$result) {
                //throw new BaseCodedException("FTP PUT Failed",BaseCodedException::DEFAULT_ERROR);
                $error = "FTP PUT Failed";
                return false;
            }
            return true;
        }, $useSSL);
    }

    /**
     * You can do any thing on FTP based on the connection established.
     * Parameter `$requestAction` would be as callable instance,
     * With definition of `boolean function($connection,&$error=null)`.
     * @param callable $requestAction
     * @param bool $useSSL
     * @return bool
     */
    public function handleRequest($requestAction, $useSSL = false)
    {
        if ($useSSL) {
            $connection = ftp_ssl_connect($this->ftpServer, $this->ftpPort, $this->ftpConnectTimeout);
        } else {
            $connection = ftp_connect($this->ftpServer, $this->ftpPort, $this->ftpConnectTimeout);
        }
        if (!$connection) {
            //throw new BaseCodedException("FTP Connection Failed",BaseCodedException::ASSERT_FAILED);
            return false;
        }
        try {
            $auth_passed = ftp_login($connection, $this->ftpUsername, $this->ftpPassword);
            ftp_pasv($connection, $this->ftpUsePassive);
            if (!$auth_passed) {
                throw new BaseCodedException("FTP Login Failed", BaseCodedException::USER_NOT_PRIVILEGED);
            }

            $error = null;
            $done = call_user_func_array($requestAction, [$connection, &$error]);
            if (!$done) {
                throw new BaseCodedException($error);
            }

            ftp_close($connection);
            return true;
        } catch (\Exception $exception) {
            ftp_close($connection);
            return false;
        }
    }

    /**
     * @param string $remotePath
     * @param bool $useSSL
     * @return bool
     */
    public function deleteFileFromFTP($remotePath, $useSSL = false)
    {
        return $this->handleRequest(function ($connection, &$error = null) use ($remotePath) {
            $result = ftp_delete($connection, $remotePath);
            if (!$result) {
                //throw new BaseCodedException("FTP PUT Failed",BaseCodedException::DEFAULT_ERROR);
                $error = "FTP PUT Failed";
                return false;
            }
            return true;
        }, $useSSL);
    }
}