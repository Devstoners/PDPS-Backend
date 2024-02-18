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
        return $this->belongsToMany(Member::class, 'members_member_positions', 'member_position_id', 'member_id')
            ->using(MembersMemberPosition::class);
    }

}


/*
    M (MemberPosition) : M (Member)
*/
