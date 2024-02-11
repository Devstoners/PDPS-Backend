<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreeCutRespond extends Model
{
    use HasFactory;
    protected $fillable = [
        'inspection_date',
        'inspection_time',
        'img1',
        'img2',
        'status',
        'tree_cut_requests_id',
    ];
}
/*
    1 (TreeCutRequest) : M (TreeCutRespondDetails)
    1 (TreeCutRequestDetail) : 1 (TreeCutRespondDetail)
    1 (TreeCutResponnd) : 1 (TreeCutRespondDetail)
*/