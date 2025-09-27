<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterUnitPrice extends Model
{
    use HasFactory;
    protected $table = 'water_unit_prices';
    protected $fillable = [
        'water_schemes_id',//0 = All schemes
        'block_no',
        'unit_price',
    ];
    public function waterScheme()
    {
        return $this->belongsTo(WaterScheme::class,'water_schemes_id');
    }
}
