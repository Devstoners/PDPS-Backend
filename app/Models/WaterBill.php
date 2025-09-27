<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterBill extends Model
{
    use HasFactory;
    protected $table = 'water_bills';
    protected $fillable = [
        'water_customer_id',
        'meter_reader_id',
        'billing_month',
        'due_date',
        'amount_due',
        'status',
    ];

    protected $casts = [
        'billing_month' => 'date',
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
    ];
    public function waterCustomer()
    {
        return $this->belongsTo(WaterCustomer::class,'water_customer_id');
    }

    public function meterReader()
    {
        return $this->belongsTo(WaterMeterReader::class,'meter_reader_id');
    }

    public function payments()
    {
        return $this->hasMany(WaterPayment::class, 'water_bill_id');
    }
}
