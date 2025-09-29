<?php

namespace App\Channels;

use App\Services\SmsNotificationService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    protected $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toSms')) {
            return $notification->toSms($notifiable);
        }

        return null;
    }
}
