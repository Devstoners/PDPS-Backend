<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'budget',
        'start_date',
        'finish_date',
        'status',
    ];
}
/*
    1 (Project) : 1 (ProjectLocale)
*/