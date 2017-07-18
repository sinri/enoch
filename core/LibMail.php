<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/28
 * Time: 10:47
 */

namespace sinri\enoch\core;


use sinri\enoch\helper\CommonHelper;
use sinri\smallphpmailer\library\PHPMailer;

/**
 * Note, @since v2.0.2 embedded `PHPMailer` library changed to `sinri\smallphpmailer`,
 * To test this library class inside this package, vendor autoload file should be required first.
 * Class LibMail
 * @package sinri\enoch\core
 */
class LibMail
{
    private $mail;
    private $smtpInfo;

    /**
     * LibMail constructor.
     * @param $params
     *
     * host,smtp_auth,username,password,smtp_secure,port,display_name
     */
    public function __construct($params = [])
    {
        $this->smtpInfo = [];

        $this->setUpSMTP($params);

        $this->mail = new PHPMailer();
    }

    /**
     * @param array $params
     */
    public function setUpSMTP($params)
    {
        $this->smtpInfo['host'] = CommonHelper::safeReadArray($params, 'host', '');
        $this->smtpInfo['smtp_auth'] = CommonHelper::safeReadArray($params, 'smtp_auth', '');
        $this->smtpInfo['username'] = CommonHelper::safeReadArray($params, 'username', '');
        $this->smtpInfo['password'] = CommonHelper::safeReadArray($params, 'password', '');
        $this->smtpInfo['smtp_secure'] = CommonHelper::safeReadArray($params, 'smtp_secure', '');
        $this->smtpInfo['port'] = CommonHelper::safeReadArray($params, 'port', '');
        $this->smtpInfo['display_name'] = CommonHelper::safeReadArray($params, 'display_name', '');
    }

    /**
     * @param int $target 0 for no debug, 4 for full debug
     * @return LibMail
     */
    public function setDebug($target = 0)
    {
        $this->mail->SMTPDebug = $target;
        return $this;
    }

    /**
     * If you are using OSX and PHP 5.6 and find error in debug, you might try on this.
     * This is the solution given by PHPMail Official GitHub Developer.
     *
     * 2017-07-18 06:00:18     Connection failed. Error #2: stream_socket_client(): SSL operation failed with code 1. OpenSSL Error messages:
     * error:14090086:SSL routines:ssl3_get_server_certificate:certificate verify failed [/Users/Sinri/Codes/Leqee/fundament/enoch/SmallPHPMail/SMTP.php line 294]
     * 2017-07-18 06:00:18     Connection failed. Error #2: stream_socket_client(): Failed to enable crypto [/Users/Sinri/Codes/Leqee/fundament/enoch/SmallPHPMail/SMTP.php line 294]
     * 2017-07-18 06:00:18     Connection failed. Error #2: stream_socket_client(): unable to connect to ssl://smtp.exmail.qq.com:465 (Unknown error) [/Users/Sinri/Codes/Leqee/fundament/enoch/SmallPHPMail/SMTP.php line 294]
     *
     * @return LibMail
     */
    public function stopSSLVerify()
    {
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        return $this;
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
     * @param null $error
     * @return bool
     */
    public function finallySend($error = null)
    {
        $done = $this->mail->send();
        $error = $this->mail->ErrorInfo;
        return $done;
    }
}