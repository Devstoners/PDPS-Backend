<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $table = 'members';

    protected $fillable = [
        'user_id','name_en', 'name_si','name_ta', 'image', 'gender', 'nic', 'tel', 'address',
        'is_married', 'member_divisions_id', 'member_parties_id', 'position'
    ];

    public function MemberDivision()
    {
        return $this->belongsTo(MemberDivision::class);
    }

    public function MemberParty()
    {
        return $this->belongsTo(MemberParty::class);
    }
}
