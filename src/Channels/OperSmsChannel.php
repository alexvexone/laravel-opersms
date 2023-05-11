<?php

namespace Alexvexone\LaravelOperSms\Channels;

use Alexvexone\LaravelOperSms\OperSmsService;
use Illuminate\Notifications\Notification;

/**
 * Драйвер для уведомлений через OperSMS
 */
class OperSmsChannel
{
    /**
     * Отправить СМС
     *
     * @param $notifiable
     * @param  Notification  $notification
     *
     * @return bool
     */
    public function send($notifiable, Notification $notification): bool
    {
        OperSmsService::send($notifiable->phone, $notification->toOperSms($notifiable));
        return true;
    }
}
