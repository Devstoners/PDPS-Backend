<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberParty extends Model
{
    use HasFactory;
    protected $fillable = [
        'party',
    ];
}
/*
    1 (MemberParty) : M (Member)
*/