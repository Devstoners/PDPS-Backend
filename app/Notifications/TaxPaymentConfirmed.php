<?php

namespace App\Notifications;

use App\Models\TaxPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaxPaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;

    public function __construct(TaxPayment $payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tax Payment Confirmed - Receipt #' . $this->payment->id)
            ->greeting('Dear ' . $this->payment->taxProperty->taxPayee->name)
            ->line('Your tax payment has been successfully processed.')
            ->line('Payment Details:')
            ->line('Receipt Number: ' . $this->payment->id)
            ->line('Amount Paid: LKR ' . number_format($this->payment->payment, 2))
            ->line('Payment Date: ' . $this->payment->pay_date->format('Y-m-d'))
            ->line('Payment Method: ' . ucfirst($this->payment->pay_method))
            ->line('Assessment Year: ' . $this->payment->taxAssessment->year)
            ->action('Download Receipt', url('/tax/payments/' . $this->payment->id . '/receipt'))
            ->line('Thank you for your payment.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'tax_payment_confirmed',
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->payment,
            'pay_date' => $this->payment->pay_date,
            'pay_method' => $this->payment->pay_method,
            'assessment_year' => $this->payment->taxAssessment->year,
        ];
    }
}
