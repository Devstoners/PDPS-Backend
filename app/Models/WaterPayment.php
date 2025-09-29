<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'water_bill_id',
        'amount_paid',
        'pay_date',
        'pay_method',
        'transaction_id',
        'receipt_no',
        'officer_id',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function waterBill()
    {
        return $this->belongsTo(WaterBill::class, 'water_bill_id');
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }
}
