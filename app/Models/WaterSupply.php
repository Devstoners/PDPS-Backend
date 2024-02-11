<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterSupply extends Model
{
    use HasFactory;
    protected $fillable = [
        'service',
        'scheme',
        'aname',
        'tele',
        'distance',
        'taxNo',
        'address'
    ];

    public function waterSupply(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WaterSupply::class, 'user_id', 'id');
    }

}
