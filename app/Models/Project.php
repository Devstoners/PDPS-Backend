<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
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
        'budget',
        'start_date',
        'finish_date',
        'status',//1 = Not started, 2 = Ongoing, 3 = Completed
    ];
}
/*
    1 (Project) : 1 (ProjectLocale)
*/
