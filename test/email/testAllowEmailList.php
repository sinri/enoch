<?php
/**
 * Created by PhpStorm.
 * User: Sven
 * Date: 2018/3/21
 * Time: 16:16
 */

require_once __DIR__. '/../../vendor/autoload.php';
require_once __DIR__ . '/../../autoload.php';

$params = [
    'host' => 'smtp.exmail.qq.com',
    'smtp_auth' => true,
    'username' => 'gfliu@leqee.com',
    'password' => 'LGF867014692lgf',
    'smtp_secure' => 'ssl',
    'port' => '465',
    'display_name' => 'OCTET测试',
    'environment' => 'test',
    'allow_email_list' => [
        'msun1@leqee.com',
        'gfliu@leqee.com'
    ]
];

$email = new \sinri\enoch\core\LibMail($params);

$smtp_error = '';
$done = $email->prepareSMTP($smtp_error)
    ->addReceiver('ljni@leqee.com', '邪恶的大鲵')
    ->addCCAddress('msun1@leqee.com', '善良的孙神')
    ->addSubject("人类真是虚无 " . __METHOD__)
    ->addHTMLBody("<h1>虚无啊</h1>")
    ->finallySend();
if ($smtp_error){
    var_dump($smtp_error);
    exit;
}
var_dump($done);
print_r("<br>");
print_r("2:<br>");

$allow_email_list = [
    'liugefengabc@163.com',
    '1050700517@qq.com'
];

$email = $email->prepareSMTP($smtp_error);
$email->setAllowEmailList($allow_email_list);
$done = $email->addReceiver('ljni@leqee.com', '邪恶的大鲵')
    ->addCCAddress('msun1@leqee.com', '善良的孙神')
    ->addCCAddress('1050700517@qq.com', '善良的孙神')
    ->addSubject("人类真是虚无 " . __METHOD__)
    ->addHTMLBody("<h1>虚无啊</h1>")
    ->finallySend();

if ($smtp_error){
    var_dump($smtp_error);
    exit;
}
var_dump($done);