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
}
/*
    M (MemberPosition) : M (Member)
*/
