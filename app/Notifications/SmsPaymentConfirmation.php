<?php

namespace App\Notifications;

use App\Services\SmsNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmsPaymentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $paymentData;
    protected $smsService;

    public function __construct(array $paymentData)
    {
        $this->paymentData = $paymentData;
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

        return $this->smsService->sendPaymentConfirmation($phone, $this->paymentData);
    }

    private function getPhoneNumber($notifiable): ?string
    {
        // Try different phone number fields
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
