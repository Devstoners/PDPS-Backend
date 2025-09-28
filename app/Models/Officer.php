<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',//1 = Mr, 2 = Mrs, 3 = Miss, 4 = Rev
        'name_en',
        'name_si',
        'name_ta',
        'image',
        'tel',
        // 'gender',
        // 'nic',
        // 'tel1',
        // 'tel2',
        // 'address',
        // 'is_married',
        'officer_services_id',
        'officer_grades_id',
        'officer_positions_id',
    ];

    /*
        1 (OfficerService) : M (Officer)
        1 (OfficerGrade) : M (Officer)
        1 (OfficerPosition) : M (Officer)
        M (OfficerSubject) : M (Officer)
        1 (User) : 1 (Officer)
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function officerService(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OfficerService::class, 'officer_services_id');
    }

    public function officerGrade(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OfficerGrade::class, 'officer_grades_id');
    }

    public function officerPosition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OfficerPosition::class, 'officer_positions_id');
    }

    public function officerSubjects()
    {
        return $this->belongsToMany(OfficerSubject::class, 'officers_officer_subjects','officers_id', 'officer_subjects_id');
    }


    public function waterBills()
    {
        return $this->hasMany(WaterBill::class, 'officer_id');
    }

    public function taxAssessments()
    {
        return $this->hasMany(TaxAssessment::class, 'officer_id');
    }

    public function taxPayments()
    {
        return $this->hasMany(TaxPayment::class, 'officer_id');
    }
}
