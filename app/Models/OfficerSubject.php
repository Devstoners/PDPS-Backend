<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerSubject extends Model
{
    use HasFactory;
    protected $table = 'officer_subjects';
    protected $fillable = [
        'subject',
    ];
}
/*
    M (OfficerSubject) : M (Officer)
*/
