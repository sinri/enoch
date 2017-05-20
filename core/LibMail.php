<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/28
 * Time: 10:47
 */

namespace sinri\enoch\core;


use sinri\enoch\helper\CommonHelper;
use sinri\enoch\SmallPHPMail\PHPMailer;

class LibMail
{
    private $mail;
    private $smtpInfo;
    private $helper;

    /**
     * LibMail constructor.
     * @param $params
     *
     * host,smtp_auth,username,password,smtp_secure,port,display_name
     */
    public function __construct($params = [])
    {
        $this->smtpInfo = [];
        $this->helper = new CommonHelper();

        $this->setUpSMTP($params);

        $this->mail = new PHPMailer();
    }

    /**
     * @param array $params
     */
    public function setUpSMTP($params)
    {
        $this->smtpInfo['host'] = $this->helper->safeReadArray($params, 'host', '');
        $this->smtpInfo['smtp_auth'] = $this->helper->safeReadArray($params, 'smtp_auth', '');
        $this->smtpInfo['username'] = $this->helper->safeReadArray($params, 'username', '');
        $this->smtpInfo['password'] = $this->helper->safeReadArray($params, 'password', '');
        $this->smtpInfo['smtp_secure'] = $this->helper->safeReadArray($params, 'smtp_secure', '');
        $this->smtpInfo['port'] = $this->helper->safeReadArray($params, 'port', '');
        $this->smtpInfo['display_name'] = $this->helper->safeReadArray($params, 'display_name', '');
    }

    /**
     * @deprecated
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
    public function sendMail($params)
    {
        $this->prepareSMTP();
        $this->mail->isSMTP();

        foreach (['to', 'reply_to', 'cc', 'bcc', 'attachment'] as $item_name) {
            if (!isset($params[$item_name])) {
                $params[$item_name] = [];
                continue;
            }
            if (!is_array($params[$item_name])) {
                $params[$item_name] = ['' => $params[$item_name]];
            }
        }

        foreach ($params['to'] as $name => $mail) {
            $this->mail->addAddress($mail, $name);
        }
        foreach ($params['reply_to'] as $name => $mail) {
            $this->mail->addReplyTo($mail, $name);
        }
        foreach ($params['cc'] as $name => $mail) {
            $this->mail->addCC($mail, $name);
        }
        foreach ($params['bcc'] as $name => $mail) {
            $this->mail->addBCC($mail, $name);
        }
        foreach ($params['attachment'] as $name => $file_path) {
            $this->mail->addAttachment($file_path, $name);
        }

        $this->mail->Subject = $params['subject'];

        $this->mail->Body = $params['body'];
        if (!isset($params['html']) || $params['html'] === false) {
            $this->mail->isHTML(true);// Set email format to HTML
            $this->mail->AltBody = $this->turnHTML2TEXT($params['body']);
        }

        $done = $this->mail->send();
        return $done;
    }

    private function turnHTML2TEXT($html)
    {
        $html = preg_replace('/\<[Bb][Rr] *\/?\>/', PHP_EOL, $html);
        $html = strip_tags($html);
        return $html;
    }

    /**
     * @return LibMail
     */
    public function prepareSMTP()
    {
        $this->mail = new PHPMailer();
        $this->mail->Host = $this->smtpInfo['host'];// Specify main and backup SMTP servers
        $this->mail->SMTPAuth = $this->smtpInfo['smtp_auth'];// Enable SMTP authentication
        $this->mail->Username = $this->smtpInfo['username'];// SMTP username
        $this->mail->Password = $this->smtpInfo['password'];// SMTP password
        $this->mail->SMTPSecure = $this->smtpInfo['smtp_secure'];// Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = $this->smtpInfo['port'];// TCP port to connect to

        $this->mail->setFrom($this->smtpInfo['username'], $this->smtpInfo['display_name']);

        $this->mail->isSMTP();
        return $this;
    }

    /**
     * @param $address
     * @param string $name
     * @return LibMail
     */
    public function addReceiver($address, $name = '')
    {
        $this->mail->addAddress($address, $name);
        return $this;
    }

    /**
     * @param $address
     * @param $name
     * @return LibMail
     */
    public function addReplyAddress($address, $name)
    {
        $this->mail->addReplyTo($address, $name);
        return $this;
    }

    /**
     * @param $address
     * @param $name
     * @return LibMail
     */
    public function addCCAddress($address, $name)
    {
        $this->mail->addCC($address, $name);
        return $this;
    }

    /**
     * @param $address
     * @param $name
     * @return LibMail
     */
    public function addBCCAddress($address, $name)
    {
        $this->mail->addBCC($address, $name);
        return $this;
    }

    /**
     * @param $filepath
     * @param string $name
     * @return LibMail
     */
    public function addAttachment($filepath, $name = '')
    {
        $this->mail->addAttachment($filepath, $name);
        return $this;
    }

    /**
     * @param $subject
     * @return LibMail
     */
    public function addSubject($subject)
    {
        $this->mail->Subject = $subject;
        return $this;
    }

    /**
     * @param $text
     * @return LibMail
     */
    public function addTextBody($text)
    {
        $this->mail->Body = $text;
        return $this;
    }

    /**
     * @param $htmlCode
     * @return LibMail
     */
    public function addHTMLBody($htmlCode)
    {
        $this->mail->isHTML(true);// Set email format to HTML
        $this->mail->Body = $htmlCode;
        $this->mail->AltBody = $this->turnHTML2TEXT($htmlCode);
        return $this;
    }

    /**
     * @return bool
     */
    public function finallySend()
    {
        $done = $this->mail->send();
        return $done;
    }
}