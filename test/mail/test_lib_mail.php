<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/7/14
 * Time: 16:51
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../autoload.php';

$mail_connection_config = [
    'host' => 'smtp.x.com',
    'smtp_auth' => true,
    'username' => 'a@x.com',
    'password' => 'x',
    'smtp_secure' => 'ssl',
    'port' => 465,
    'display_name' => 'Enoch',
];

if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

print_r($mail_connection_config);

$mailer = new \sinri\enoch\core\LibMail($mail_connection_config);
$done = $mailer->prepareSMTP($error1)
    ->addReceiver('ljni@leqee.com', 'Killer')
    ->addSubject("Test for PHPMail 6.0")
    ->addTextBody('Yeah!')
    ->stopSSLVerify()
    ->finallySend($error2);
var_dump($done);
var_dump($error1);
var_dump($error2);