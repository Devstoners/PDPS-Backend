<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembersMemberPosition extends Model
{
    use HasFactory;
    protected $fillable = [
        'members_id',
        'member_positions_id',
    ];
}
