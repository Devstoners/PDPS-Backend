<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberParty extends Model
{
    use HasFactory;
    protected $table = 'member_parties';
    protected $fillable = [
        'party_en',
        'party_si',
        'party_ta',
    ];
    public function Member()
    {
        return $this->hasMany(Member::class);
    }
}
/*
    1 (MemberParty) : M (Member)
*/
