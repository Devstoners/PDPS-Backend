<?php

namespace App\Notifications;

use App\Models\TaxAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaxAssessmentCreated extends Notification implements ShouldQueue
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
            ->subject('New Tax Assessment - ' . $this->assessment->year)
            ->greeting('Dear ' . $this->assessment->taxProperty->taxPayee->name)
            ->line('A new tax assessment has been created for your property.')
            ->line('Assessment Details:')
            ->line('Year: ' . $this->assessment->year)
            ->line('Amount: LKR ' . number_format($this->assessment->amount, 2))
            ->line('Due Date: ' . $this->assessment->due_date->format('Y-m-d'))
            ->action('View Assessment', url('/tax/assessments/' . $this->assessment->id))
            ->line('Please ensure payment is made before the due date to avoid penalties.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'tax_assessment_created',
            'assessment_id' => $this->assessment->id,
            'year' => $this->assessment->year,
            'amount' => $this->assessment->amount,
            'due_date' => $this->assessment->due_date,
            'property_name' => $this->assessment->taxProperty->property_name,
        ];
    }
}
