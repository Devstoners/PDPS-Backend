<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_facility_id',
        'rate',
        'rate_type',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
    ];

    public function hallFacility()
    {
        return $this->belongsTo(HallFacility::class);
    }
}
