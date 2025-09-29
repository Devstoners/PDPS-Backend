<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'tel',
        'capacity',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'hall_facilities');
    }

    public function hallFacilities()
    {
        return $this->hasMany(HallFacility::class);
    }

    public function reservations()
    {
        return $this->hasMany(HallReservation::class);
    }
}
