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

    public function __construct($params)
    {
        if (isset($params['server'])) {
            $this->strServer=$params['server'];
        }
        if (isset($params['port'])) {
            $this->strServerPort=$params['port'];
        }
        if (isset($params['username'])) {
            $this->strServerUsername=$params['username'];
        }
        if (isset($params['password'])) {
            $this->strServerPassword=$params['password'];
        }
    }

    public function sendFileToSFtp($filename, $remoteDir, $localDir, &$error = '')
    {
        //$done = false;
        $sftpStream = null;
        try {
            $resConnection = ssh2_connect($this->strServer, $this->strServerPort);

            if (!$resConnection) {
                throw new \Exception(
                    "Failed to link to SSH2 server: " .
                    $this->strServer . ":" . $this->strServerPort . "!"
                );
            }

//            if (ssh2_auth_password($resConnection, $this->strServerUsername, $this->strServerPassword)) {
//                //初始化SFTP子系统
//                //请求从一个已经连接子系统SFTP服务器SSH2安全性会更高。
//                $resSFTP = ssh2_sftp($resConnection);
//            } else {
//                throw new \Exception("Auth Failed");
//            }

            $auth_passed = ssh2_auth_password($resConnection, $this->strServerUsername, $this->strServerPassword);
            if (!$auth_passed) {
                throw new \Exception("Auth Failed");
            }
            //初始化SFTP子系统
            //请求从一个已经连接子系统SFTP服务器SSH2安全性会更高。
            $resSFTP = ssh2_sftp($resConnection);

            $remote_path = $remoteDir . '/' . $filename;
            $local_path = $localDir . '/' . $filename;

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

    public function downloadAndRemoveDir($remoteDir, $localPath, &$doneFiles = [], &$error = '')
    {
        //$done=false;
        $handler = null;
        try {
            $resConnection = ssh2_connect($this->strServer, $this->strServerPort);
            if (!$resConnection) {
                throw new \Exception("Cound not connect: ".$this->strServer.":".$this->strServerPort);
            }
            if (ssh2_auth_password($resConnection, $this->strServerUsername, $this->strServerPassword)) {
                $resSFTP = ssh2_sftp($resConnection);
            } else {
                throw new \Exception("ssh2_auth_password false");
            }

            //$file_name = '';
            $handler = opendir('ssh2.sftp://' . $resSFTP . $remoteDir);

            $i = 0;
            $files = array();
            while (($i<5) && (($file_name = readdir($handler)) !== false)) {//务必使用!==，防止目录下出现类似文件名“0”等情况
                if ($file_name != "." && $file_name != ".." && $file_name != "archive") {
                    $files[] = $file_name ;

                    $remote_file = $remoteDir . $file_name;
                    $local_file = $localPath . $file_name;

                    //将远程文件保存到本地
                    $content = file_get_contents('ssh2.sftp://' . $resSFTP . $remoteDir . $file_name, 'rw');
                    //$result2 = '';
                    $get_content = iconv('gbk', 'utf-8', $content);

                    $data_to_write = fopen($local_file, 'w+');
                    fwrite($data_to_write, $get_content);

                    fclose($data_to_write);

                    //将远程文件删除
                    // sh2_sftp_unlink($resSFTP, $remote_file);
                    //移动远程文件
                    $remove_file = $remoteDir . 'archive/' . $file_name;
                    //$res =
                    ssh2_sftp_rename($resSFTP, $remote_file, $remove_file);

                    //ClsTools::LogRecord("文件已下载 ： " .$file_name . "  文件移动:".$res ."</br>");
                    $doneFiles[] = $file_name;

                    $i++;
                }
            }
            $done=true;
        } catch (\Exception $e) {
            $error = 'Method '.__METHOD__.' Exception: '. $e->getMessage();
            $done=false;
        }
        closedir($handler);
        return $done;
    }
}
