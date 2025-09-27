<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxAssessment extends Model
{
    use HasFactory;
    
    protected $table = 'tax_assessments';
    
    protected $fillable = [
        'tax_property_id',
        'year',
        'amount',
        'due_date',
        'status',
        'officer_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function taxProperty()
    {
        return $this->belongsTo(TaxProperty::class, 'tax_property_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function taxPayments()
    {
        return $this->hasMany(TaxPayment::class, 'tax_assessment_id');
    }

    public function penaltyNotices()
    {
        return $this->hasMany(TaxPenaltyNotice::class, 'assessment_id');
    }

    public function isOverdue()
    {
        return $this->due_date < now()->toDateString() && $this->status !== 'paid';
    }

    public function getTotalPaidAttribute()
    {
        return $this->taxPayments()->where('status', 'confirmed')->sum('payment');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->total_paid;
    }
}
