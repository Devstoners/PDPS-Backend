<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterScheme extends Model
{
    use HasFactory;
    protected $table = 'water_schemes';
    protected $fillable = [
        'division_id',
        'name',
        'start_date',
    ];
    public function division()
    {
        return $this->belongsTo(Division::class,'division_id');
    }

    public function waterMeterReaders()
    {
        return $this->hasMany(WaterMeterReader::class, 'water_schemes_id');
    }

    public function waterCustomers()
    {
        return $this->hasMany(WaterCustomer::class, 'water_schemes_id');
    }

    public function waterUnitPrices()
    {
        return $this->hasMany(WaterUnitPrice::class, 'water_schemes_id');
    }
}
