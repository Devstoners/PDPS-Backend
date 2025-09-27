<?php

namespace App\Notifications;

use App\Models\PropertyProhibitionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProhibitionOrderIssued extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(PropertyProhibitionOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Property Prohibition Order Issued')
            ->greeting('Dear ' . $this->order->taxProperty->taxPayee->name)
            ->line('A prohibition order has been issued for your property.')
            ->line('Property Details:')
            ->line('Property Name: ' . $this->order->taxProperty->property_name)
            ->line('Address: ' . $this->order->taxProperty->street)
            ->line('Order Date: ' . $this->order->order_date->format('Y-m-d'))
            ->line('This order prohibits certain activities on your property until tax obligations are fulfilled.')
            ->action('View Details', url('/tax/properties/' . $this->order->tax_property_id))
            ->line('Please contact the tax office for more information.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'prohibition_order_issued',
            'order_id' => $this->order->id,
            'property_name' => $this->order->taxProperty->property_name,
            'order_date' => $this->order->order_date,
            'tax_property_id' => $this->order->tax_property_id,
        ];
    }
}
