<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_id',
        'facility_id',
    ];

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function rates()
    {
        return $this->hasMany(HallRate::class);
    }
}
