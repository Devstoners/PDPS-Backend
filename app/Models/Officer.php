<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'full_name',
        'display_name',
        'profile_pic',
        'gender',
        'nic',
        'tel1',
        'tel2',
        'address',
        'is_married',
        'is_registered',
        'officer_positions_id',
    ];

    /*
        1 (OfficerPosition) : M (Officer)
        M (OfficerSubject) : M (Officer)
        1 (User) : 1 (Officer)
    */
    public function officerPosition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OfficerPosition::class, 'officer_position_id');
    }
}
