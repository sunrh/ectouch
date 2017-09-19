<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=ectouch',
    'username' => 'homestead',
    'password' => 'secret',
    'charset' => 'utf8',
    'tablePrefix' => 'ecs_',
    'on afterOpen' => function($event) {
        $event->sender->createCommand("set session sql_mode='NO_ENGINE_SUBSTITUTION'")->execute();
    }
];
