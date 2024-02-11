<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLocale extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_si',
        'name_en',
        'name_ta',
        'description_si',
        'description_en',
        'description_ta',
        'executor_si',
        'executor_en',
        'executor_ta',
    ];
}
/*
    1 (Project) : 1 (ProjectLocale)
*/