<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPenaltyNotice extends Model
{
    use HasFactory;
    
    protected $table = 'tax_penalty_notices';
    
    protected $fillable = [
        'assessment_id',
        'issue_date',
        'penalty_amount',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'penalty_amount' => 'decimal:2',
    ];

    public function assessment()
    {
        return $this->belongsTo(TaxAssessment::class, 'assessment_id');
    }
}
