<?php

namespace App\Notifications;

use App\Services\SmsNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmsReservationConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservationData;
    protected $smsService;

    public function __construct(array $reservationData)
    {
        $this->reservationData = $reservationData;
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

        return $this->smsService->sendReservationConfirmation($phone, $this->reservationData);
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
