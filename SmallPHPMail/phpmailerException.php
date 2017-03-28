<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/28
 * Time: 10:56
 */

namespace sinri\enoch\SmallPHPMail;

/**
 * PHPMailer exception handler
 * @package PHPMailer
 */
class phpmailerException extends \Exception
{
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}
