<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerLevel extends Model
{
    use HasFactory;
    protected $fillable = [
        'level_en',
        'level_si',
        'level_ta',
    ];

    public function subjects()
    {
        return $this->hasMany(OfficerSubject::class, 'officer_levels_id');
    }

    public function positions()
    {
        return $this->hasMany(OfficerPosition::class, 'officer_levels_id');
    }


}
