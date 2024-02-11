<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GramaDivision extends Model
{
    use HasFactory;
    protected $fillable = [
        'division',
    ];
}
/*
    1 (Grama) : 1 (GramaDivision)
*/