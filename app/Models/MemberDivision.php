<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberDivision extends Model
{
    use HasFactory;
    protected $fillable = [
        'division',
    ];
}
/*
    1 (MemberDivision) : M (Member)
*/