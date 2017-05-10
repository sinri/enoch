<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 11:13
 */

namespace sinri\enoch\test\WalkingSystem\sample;

use sinri\enoch\core\LibLog;
use sinri\enoch\core\LibMail;
use sinri\enoch\core\Walker;

class SendOrderWalker extends Walker
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
        $this->logger->log(LibLog::LOG_INFO, __METHOD__ . "@" . __LINE__);
        $this->testSendMail();
        return true;
    }

    private function testSendMail(){
        $mail_connection_config=[
            'host'=>'smtp.exmail.qq.com',
            'smtp_auth'=>true,
            'username'=>'you@me.com',
            'password'=>'thy_password',
            'smtp_secure'=>'ssl',
            'port'=>465,
            'display_name'=>'Enoch',
        ];
        if (file_exists(__DIR__ . '/not_commit_config.php')) {
            require __DIR__ . '/not_commit_config.php';
        }
        $mailer=new LibMail($mail_connection_config);

        /*
        $mail_info=[
            "to" => 'ljni@leqee.com',
            "subject" => "Enoch Test Mail Old Style",
            "body"=>"And Enoch walked with God: and he <i>[was]</i> not; for God took him.",
        ];
        $done=$mailer->sendMail($mail_info);
        */

        $done = $mailer->prepareSMTP()
            ->addReceiver("ljni@leqee.com", "ljni")
            ->addSubject("Enoch Test Mail")
            ->addHTMLBody("And Enoch walked with God: and he <i>[was]</i> not; for God took him.")
            ->finallySend();


        $this->logger->log(($done ? LibLog::LOG_INFO : LibLog::LOG_ERROR), "Sending mail", $done);
    }
}
