<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function halls()
    {
        return $this->belongsToMany(Hall::class, 'hall_facilities');
    }

    public function hallFacilities()
    {
        return $this->hasMany(HallFacility::class);
    }
}
