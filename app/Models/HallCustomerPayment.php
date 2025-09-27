<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallCustomerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_reservation_id',
        'pay_amount',
        'pay_date',
        'pay_method',
        'transaction_id',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'pay_amount' => 'decimal:2',
        'pay_date' => 'date',
    ];

    public function reservation()
    {
        return $this->belongsTo(HallReservation::class, 'hall_reservation_id');
    }
}
