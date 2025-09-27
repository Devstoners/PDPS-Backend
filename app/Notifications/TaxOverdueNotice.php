<?php

namespace App\Notifications;

use App\Models\TaxAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaxOverdueNotice extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assessment;

    public function __construct(TaxAssessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tax Payment Overdue - Immediate Action Required')
            ->greeting('Dear ' . $this->assessment->taxProperty->taxPayee->name)
            ->line('Your tax payment is now overdue.')
            ->line('Assessment Details:')
            ->line('Year: ' . $this->assessment->year)
            ->line('Amount Due: LKR ' . number_format($this->assessment->amount, 2))
            ->line('Due Date: ' . $this->assessment->due_date->format('Y-m-d'))
            ->line('Days Overdue: ' . $this->assessment->due_date->diffInDays(now()))
            ->line('Please make payment immediately to avoid further penalties.')
            ->action('Pay Now', url('/tax/payments/online/' . $this->assessment->id))
            ->line('Late payment penalties may apply.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'tax_overdue_notice',
            'assessment_id' => $this->assessment->id,
            'year' => $this->assessment->year,
            'amount' => $this->assessment->amount,
            'due_date' => $this->assessment->due_date,
            'days_overdue' => $this->assessment->due_date->diffInDays(now()),
        ];
    }
}
