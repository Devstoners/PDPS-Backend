<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterMeterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'water_customer_id',
        'reading_month',
        'current_reading',
        'previous_reading',
        'units_consumed',
    ];

    protected $casts = [
        'reading_month' => 'date',
        'current_reading' => 'decimal:2',
        'previous_reading' => 'decimal:2',
        'units_consumed' => 'decimal:2',
    ];

    public function waterCustomer()
    {
        return $this->belongsTo(WaterCustomer::class, 'water_customer_id');
    }
}
