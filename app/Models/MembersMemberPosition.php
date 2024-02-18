<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MembersMemberPosition extends Pivot
{
    use HasFactory;
    protected $table = 'members_member_positions';
    protected $fillable = [
        'member_id',
        'member_position_id',
    ];
}
