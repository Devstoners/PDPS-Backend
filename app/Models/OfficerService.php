<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerService extends Model
{
    use HasFactory;
    protected $fillable = [
        'sname_en',
        'sname_si',
        'sname_ta',
    ];

    public function grades()
    {
        return $this->hasMany(OfficerGrade::class, 'officer_services_id');
    }

    public function positions()
    {
        return $this->hasMany(OfficerPosition::class, 'officer_services_id');
    }

    public function officers()
    {
        return $this->hasMany(Officer::class, 'officer_services_id');
    }

}
