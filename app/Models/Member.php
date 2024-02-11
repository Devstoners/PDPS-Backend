<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $table = 'members';

    protected $fillable = [
        'email', 'user_id','full_name', 'display_name', 'profile_pic', 'gender', 'nic', 'tel1', 'tel2', 'address',
        'is_married', 'is_registered', 'member_divisions_id', 'member_parties_id'
    ];
}
