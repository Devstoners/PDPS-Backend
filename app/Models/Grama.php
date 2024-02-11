<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grama extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'full_name',
        'display_name',
        'profile_pic',
        'gender',
        'nic',
        'tel1',
        'tel2',
        'is_married',
        'is_registered',
        'grama_divisions_id',
    ];
}
/*
    1 (Grama) : 1 (GramaDivision)
    1 (User) : 1 (Grama)
*/