<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 11:36
 */

namespace sinri\enoch\core;


class LibSFTP
{
    protected $strServer = "";
    protected $strServerPort = "22";
    protected $strServerUsername = "";
    protected $strServerPassword = "";

    function __construct($params)
    {
        if(isset($params['server'])){
            $this->strServer=$params['server'];
        }
        if(isset($params['port'])){
            $this->strServerPort=$params['port'];
        }
        if(isset($params['username'])){
            $this->strServerUsername=$params['username'];
        }
        if(isset($params['password'])){
            $this->strServerPassword=$params['password'];
        }
    }

    public function sendFileToSFtp($filename,$REMOTE_DIR,$LOCAL_DIR,&$error='')
    {
        $done = false;
        $sftpStream = null;
        try {
            $resConnection = ssh2_connect($this->strServer, $this->strServerPort);

            if (!$resConnection) {
                throw new \Exception(
                    "Failed to link to SSH2 server: " .
                    $this->strServer . ":" . $this->strServerPort . "!"
                );
            }

            if (ssh2_auth_password($resConnection, $this->strServerUsername, $this->strServerPassword)) {
                //初始化SFTP子系统
                //请求从一个已经连接子系统SFTP服务器SSH2安全性会更高。
                $resSFTP = ssh2_sftp($resConnection);
            } else {
                throw new \Exception("Auth Failed");
            }

            $remote_path = $REMOTE_DIR . '/' . $filename;
            $local_path = $LOCAL_DIR . '/' . $filename;

            $sftpStream = fopen('ssh2.sftp://' . $resSFTP . $remote_path, 'w');

            if (!$sftpStream) {
                throw new \Exception("Could not open remote file: " . $remote_path);
            }

            $data_to_send = file_get_contents($local_path);

            if ($data_to_send === false) {
                throw new \Exception("Could not open local file: " . $local_path);
            }

            if (fwrite($sftpStream, $data_to_send) === false) {
                throw new \Exception("Could not send data from file: ");
            }
            $done = true;
        } catch (\Exception $e) {
            $error = __METHOD__ . ' filename:' . $filename . ' Exception: ' . $e->getMessage();
            $done = false;
        }
        //finally {//this keyword is not available until PHP 5.5
        fclose($sftpStream);
        //}
        return $done;
    }
}