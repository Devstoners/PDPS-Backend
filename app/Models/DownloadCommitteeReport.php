<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadCommitteeReport extends Model
{
    use HasFactory;
    protected $table = 'download_committee_reports';
    protected $fillable = [
        'year',
        'month',
        'name_en',
        'name_si',
        'name_ta',
        'file_path',
    ];
}
