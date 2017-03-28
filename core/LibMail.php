<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/28
 * Time: 10:47
 */

namespace sinri\enoch\core;


use sinri\enoch\SmallPHPMail\PHPMailer;

class LibMail
{
    private $mail;

    /**
     * LibMail constructor.
     * @param $params
     *
     * host,smtp_auth,username,password,smtp_secure,port,display_name
     */
    public function __construct($params)
    {
        $this->mail = new PHPMailer();
        $this->mail->Host = $params['host'];// Specify main and backup SMTP servers
        $this->mail->SMTPAuth = $params['smtp_auth'];// Enable SMTP authentication
        $this->mail->Username = $params['username'];// SMTP username
        $this->mail->Password = $params['password'];// SMTP password
        $this->mail->SMTPSecure = $params['smtp_secure'];// Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = $params['port'];// TCP port to connect to

        $this->mail->setFrom($params['username'], $params['display_name']);
    }

    /**
     * @param $params
     * @return bool
     *
     * PARAMETERS
     * 1. to: String or Array(name=>mail)
     * 2. reply_to: String or Array(name=>mail)
     * 3. cc: String or Array(name=>mail)
     * 4. bcc: String or Array(name=>mail)
     * 5. attachment: String or Array(name=>path)
     * 6. subject: String
     * 7. html: Boolean true for default
     * 8. body: String
     */
    public function sendMail($params){
        $this->mail->isSMTP();

        if(is_array($params['to'])){
            foreach ($params['to'] as $name => $mail){
                $this->mail->addAddress($mail,$name);
            }
        }
        else{
            $this->mail->addAddress($params['to']);
        }
        if(isset($params['reply_to'])){
            if(is_array($params['reply_to'])){
                foreach ($params['reply_to'] as $name => $mail){
                    $this->mail->addReplyTo($mail,$name);
                }
            }
            else{
                $this->mail->addReplyTo($params['reply_to']);
            }
        }
        if(isset($params['cc'])){
            if(is_array($params['cc'])){
                foreach ($params['cc'] as $name => $mail){
                    $this->mail->addCC($mail,$name);
                }
            }
            else{
                $this->mail->addCC($params['cc']);
            }
        }
        if(isset($params['bcc'])){
            if(is_array($params['bcc'])){
                foreach ($params['bcc'] as $name => $mail){
                    $this->mail->addBCC($mail,$name);
                }
            }
            else{
                $this->mail->addBCC($params['bcc']);
            }
        }

        if(isset($params['attachment'])){
            if(is_array($params['attachment'])){
                foreach ($params['attachment'] as $name => $file_path){
                    $this->mail->addAttachment($file_path,$name);
                }
            }
            else{
                $this->mail->addAttachment($params['attachment']);
            }
        }

        $this->mail->Subject = $params['subject'];

        if(isset($params['html']) && $params['html']===false) {
            $this->mail->Body = $params['body'];
        }else{
            $this->mail->isHTML(true);// Set email format to HTML
            $this->mail->Body = $params['body'];
            $this->mail->AltBody = $this->turnHTML2TEXT($params['body']);
        }

        $done=$this->mail->send();
        return $done;
    }

    private function turnHTML2TEXT($html){
        $html=preg_replace('/\<[Bb][Rr] *\/?\>/',PHP_EOL,$html);
        $html=strip_tags($html);
        return $html;
    }
}