<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberPosition extends Model
{
    use HasFactory;
    protected $fillable = [
        'position_en',
        'position_si',
        'position_ta',
    ];
    public function members()
    {
        return $this->belongsToMany(Member::class, 'members_member_positions', 'member_positions_id', 'members_id');
    }

}


/*
    M (MemberPosition) : M (Member)
*/
