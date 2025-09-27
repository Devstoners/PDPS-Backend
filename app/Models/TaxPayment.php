<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPayment extends Model
{
    use HasFactory;
    
    protected $table = 'tax_payments';
    
    protected $fillable = [
        'tax_property_id',
        'tax_assessment_id',
        'officer_id',
        'discount_amount',
        'fine_amount',
        'pay_date',
        'pay_method',
        'payment',
        'transaction_id',
        'status',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'payment' => 'decimal:2',
    ];

    public function taxProperty()
    {
        return $this->belongsTo(TaxProperty::class, 'tax_property_id');
    }

    public function taxAssessment()
    {
        return $this->belongsTo(TaxAssessment::class, 'tax_assessment_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function getNetPaymentAttribute()
    {
        return $this->payment - $this->discount_amount + $this->fine_amount;
    }
}
