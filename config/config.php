<?php

$params = require(__DIR__ . '/app.php');
$shop = require(__DIR__ . '/shop.php');
$db = require(__DIR__ . '/database.php');

/**
 * Load Routes Configuration
 */
$web = require(__DIR__ . '/../routes/web.php');
$dashboard = require(__DIR__ . '/../routes/dashboard.php');
$api = require(__DIR__ . '/../routes/api.php');

foreach ($api as $version => $rules) {
    foreach ($rules as $name => $rule) {
        $web['api/' . $version . '/' . $name] = 'api/' . $version . '/' . $rule;
    }
}

foreach ($dashboard as $key => $vo) {
    $admin[ADMIN_PATH . '/' . $key] = 'admin/' . $vo;
}

$rules = array_merge($web, $admin);

$config = [
    'id' => 'ectouch',
    'basePath' => '@app',
    'viewPath' => '@view',
    'runtimePath' => '@runtime',
    'vendorPath' => '@vendor',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'App\Http\Controllers',
    'defaultRoute' => 'index',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '<secret random string goes here>',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => '127.0.0.1:9200'],
                // configure more hosts if you have a cluster
            ],
        ],
        'admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\AdminUser',
            'enableAutoLogin' => true,
            'loginUrl' => ['admin/login/index'],
            'identityCookie' => ['name' => '_admin_identity', 'httpOnly' => true]
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/login/index'],
            'identityCookie' => ['name' => '_identity', 'httpOnly' => true]
        ],
        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@storage/logs/app.log',
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $rules,
        ],
    ],
    'modules' => require(__DIR__ . '/module.php'),
    'params' => array_merge($params, $shop),
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '192.168.10.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '192.168.10.1'],
    ];
}

return $config;
