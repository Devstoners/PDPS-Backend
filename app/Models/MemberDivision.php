<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberDivision extends Model
{
    use HasFactory;
    protected $table = 'member_divisions';
    protected $fillable = [
        'division_en',
        'division_si',
        'division_ta',
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'member_divisions_id');
    }


}

