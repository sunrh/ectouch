<?php

namespace App\Notifications;

/**
 * 通知服务抽象构造
 * Class Notification
 * @package App\Notifications
 */
abstract class Notification
{
    protected $via = [];

    /**
     * 发送通知
     */
    public function send()
    {

        foreach ($this->via as $via) {

        }

    }

}