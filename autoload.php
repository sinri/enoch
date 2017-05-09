<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 10:27
 */
require_once __DIR__ . '/helper/CommonHelper.php';
spl_autoload_register(function ($class_name) {
    $ch = new \sinri\enoch\helper\CommonHelper();
    $file_path = $ch->getFilePathOfClassNameWithPSR0(
        $class_name,
        'sinri\enoch',
        __DIR__,
        '.php'
    );
    if ($file_path) {
        require_once $file_path;
    }
});

/*
//OLD STYLE
require_once __DIR__ . '/core/LibConsoleColor.php';
require_once __DIR__ . '/core/LibLog.php';
require_once __DIR__.'/core/Spirit.php';
require_once __DIR__.'/core/Enoch.php';
require_once __DIR__.'/core/Walker.php';
require_once __DIR__.'/core/LibMySQL.php';
require_once __DIR__.'/core/LibSFTP.php';
require_once __DIR__ . '/core/LibSession.php';

require_once __DIR__.'/SmallPHPMail/phpmailerException.php';
require_once __DIR__.'/SmallPHPMail/class.phpmailer.php';
require_once __DIR__.'/SmallPHPMail/class.smtp.php';

require_once __DIR__.'/core/LibMail.php';

require_once __DIR__ . '/mvc/BaseCodedException.php';
require_once __DIR__ . '/mvc/ApiInterface.php';
require_once __DIR__ . '/mvc/Lamech.php';
require_once __DIR__ . '/mvc/RouterInterface.php';
require_once __DIR__ . '/mvc/Naamah.php';
require_once __DIR__ . '/mvc/Adah.php';
require_once __DIR__ . '/mvc/MiddlewareInterface.php';

require_once __DIR__ . '/service/QueueItem.php';
require_once __DIR__ . '/service/QueueInterface.php';

require_once __DIR__ . '/service/CacheInterface.php';
require_once __DIR__ . '/service/FileCache.php';
*/