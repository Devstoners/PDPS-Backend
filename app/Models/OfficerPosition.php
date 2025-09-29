<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerPosition extends Model
{
    use HasFactory;
    protected $table = 'officer_positions';
    protected $fillable = [
        'position_en',
        'position_si',
        'position_ta',
        'officer_services_id',
        'officer_levels_id',
    ];
    /*
        1 (OfficerPosition) : M (Officer)
    */
    public function officers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Officer::class, 'officer_positions_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(OfficerService::class, 'officer_services_id');
    }

    public function level()
    {
        return $this->belongsTo(OfficerLevel::class, 'officer_levels_id');
    }

}


