<?php

if (version_compare(PHP_VERSION, '5.6.4', '<')) {
    die('require PHP > 5.6.4 !');
}

/*
|--------------------------------------------------------------------------
| Setting Version
|--------------------------------------------------------------------------
|
*/

define('APPNAME', 'ECTouch');
define('VERSION', '2.0.0-dev');
define('RELEASE', '20170919');
define('CHARSET', 'utf-8');
define('ADMIN_PATH', 'admin');
define('AUTH_KEY', 'this is a key');
define('OLD_AUTH_KEY', '');
define('API_TIME', '2017-09-19 09:20:18');

/*
|--------------------------------------------------------------------------
| Setting Debuger
|--------------------------------------------------------------------------
|
*/

if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '192.168.10.1'])) {
    defined('YII_DEBUG') or define('YII_DEBUG', false);
    defined('YII_ENV') or define('YII_ENV', 'prod');
} else {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
}

/*
|--------------------------------------------------------------------------
| Loading Kernel
|--------------------------------------------------------------------------
|
*/

require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

/*
|--------------------------------------------------------------------------
| Loading Bootstrap
|--------------------------------------------------------------------------
|
*/

require(__DIR__ . '/../config/bootstrap.php');

/*
|--------------------------------------------------------------------------
| Loading Configuration
|--------------------------------------------------------------------------
|
*/

$config = require(__DIR__ . '/../config/config.php');

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return new yii\web\Application($config);
