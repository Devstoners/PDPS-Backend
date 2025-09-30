<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterMeterReader extends Model
{
    use HasFactory;
    protected $table = 'water_meter_readers';
    protected $fillable = [
        'user_id',
        'water_schemes_id',
    ];
    public function waterScheme()
    {
        return $this->belongsTo(WaterScheme::class,'water_schemes_id');
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'user_id');
    }

    public function waterBills()
    {
        return $this->hasMany(WaterBill::class, 'meter_reader_id');
    }
}
