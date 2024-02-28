<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerSubject extends Model
{
    use HasFactory;
    protected $table = 'officer_subjects';
    protected $fillable = [
        'subject_en',
        'subject_si',
        'subject_ta',
    ];
}
/*
    M (OfficerSubject) : M (Officer)
*/
