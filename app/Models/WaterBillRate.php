<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterBillRate extends Model
{
    use HasFactory;
    
    protected $table = 'water_bill_rates';
    
    protected $fillable = [
        'water_schemes_id',
        'units_0_1',
        'units_1_5',
        'units_above_5',
        'service',
    ];

    protected $casts = [
        'units_0_1' => 'decimal:2',
        'units_1_5' => 'decimal:2',
        'units_above_5' => 'decimal:2',
        'service' => 'decimal:2',
    ];

    public function waterScheme()
    {
        return $this->belongsTo(WaterScheme::class, 'water_schemes_id');
    }

    public function division()
    {
        return $this->hasOneThrough(
            Division::class,
            WaterScheme::class,
            'id', // Foreign key on water_schemes table
            'id', // Foreign key on divisions table
            'water_schemes_id', // Local key on water_bill_rates table
            'division_id' // Local key on water_schemes table
        );
    }
}