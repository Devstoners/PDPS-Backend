<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;
    protected $table = 'divisions';
    protected $fillable = [
        'division_en',
        'division_si',
        'division_ta',
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'divisions_id');
    }

    public function waterSchemes()
    {
        return $this->hasMany(WaterScheme::class, 'division_id');
    }

    public function taxProperties()
    {
        return $this->hasMany(TaxProperty::class, 'division_id');
    }
}

