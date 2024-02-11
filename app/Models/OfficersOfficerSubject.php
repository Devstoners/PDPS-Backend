<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficersOfficerSubject extends Model
{
    use HasFactory;
    protected $fillable = [
        'officers_id',
        'officer_subjects_id',
    ];
}
