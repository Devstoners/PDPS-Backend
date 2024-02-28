<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MembersMemberPosition extends Pivot
{
    use HasFactory;
    protected $table = 'members_member_positions';
    protected $fillable = [
        'members_id',
        'member_positions_id',
    ];
}
