<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerPosition extends Model
{
    use HasFactory;
    protected $table = 'officer_positions';
    protected $fillable = [
        'position',
    ];
    /*
        1 (OfficerPosition) : M (Officer)
    */
    public function officers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Officer::class, 'officer_positions_id', 'id');
    }
}


