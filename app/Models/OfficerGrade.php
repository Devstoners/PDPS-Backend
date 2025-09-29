<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerGrade extends Model
{
    use HasFactory;
    protected $fillable = [
        'grade_en',
        'grade_si',
        'grade_ta',
        'officer_services_id',
    ];

    public function service()
    {
        return $this->belongsTo(OfficerService::class, 'officer_services_id');
    }

    public function officers()
    {
        return $this->hasMany(Officer::class, 'officer_grades_id');
    }


}
