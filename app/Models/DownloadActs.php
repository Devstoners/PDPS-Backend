<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadActs extends Model
{
    use HasFactory;
    protected $table = 'download_acts';
    protected $fillable = [
        'number',
        'issue_date',
        'name_en',
        'name_si',
        'name_ta',
        'file_path_en',
        'file_path_si',
        'file_path_ta',
    ];
}
