<?php

namespace App\Notifications;

use App\Services\SmsNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmsOverdueNotice extends Notification implements ShouldQueue
{
    use Queueable;

    protected $overdueData;
    protected $smsService;

    public function __construct(array $overdueData)
    {
        $this->overdueData = $overdueData;
        $this->smsService = new SmsNotificationService();
    }

    public function via($notifiable)
    {
        return ['sms'];
    }

    public function toSms($notifiable)
    {
        $phone = $this->getPhoneNumber($notifiable);
        
        if (!$phone) {
            return;
        }

        return $this->smsService->sendOverdueNotice($phone, $this->overdueData);
    }

    private function getPhoneNumber($notifiable): ?string
    {
        if (isset($notifiable->phone)) {
            return $notifiable->phone;
        }
        
        if (isset($notifiable->tel)) {
            return $notifiable->tel;
        }
        
        if (isset($notifiable->mobile)) {
            return $notifiable->mobile;
        }
        
        if (isset($notifiable->customer_phone)) {
            return $notifiable->customer_phone;
        }
        
        return null;
    }
}
