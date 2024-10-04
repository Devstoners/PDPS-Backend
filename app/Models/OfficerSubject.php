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
        'officer_levels_id',
    ];

    public function level()
    {
        return $this->belongsTo(OfficerLevel::class, 'officer_levels_id');
    }

    public function officers()
    {
        return $this->belongsToMany(Officer::class, 'officers_officer_subjects', 'officers_id','officer_subjects_id');
    }

}
/*
    M (OfficerSubject) : M (Officer)
*/
