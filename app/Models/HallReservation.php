<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_id',
        'hall_customer_id',
        'start_datetime',
        'end_datetime',
        'status',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function customer()
    {
        return $this->belongsTo(HallCustomer::class, 'hall_customer_id');
    }

    public function payments()
    {
        return $this->hasMany(HallCustomerPayment::class);
    }
}
