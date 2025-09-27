<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $table = 'members';

    protected $fillable = [
        'user_id',
        'title',//1 = Mr, 2 = Mrs, 3 = Miss, 4 = Rev
        'name_en',
        'name_si',
        'name_ta',
        'image',
        'tel',
//        'gender',
//        'nic',
//        'address',
//        'is_married',
        'divisions_id',
        'member_parties_id',
        ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'divisions_id');
    }

    public function memberParty()
    {
        return $this->belongsTo(MemberParty::class, 'member_parties_id');
    }

    public function memberPositions()
    {
        return $this->belongsToMany(MemberPosition::class, 'members_member_positions', 'members_id', 'member_positions_id');
    }

}
