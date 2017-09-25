<?php

namespace app\notifications;

use app\channels\SmsChannel;

class InvoicePaid extends Notification
{
    use Queueable;

    /**
     * 获取通知频道
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * 获取通知的声音展示方式
     *
     * @param  mixed  $notifiable
     * @return VoiceMessage
     */
    public function toVoice($notifiable)
    {
        // ...
    }
}