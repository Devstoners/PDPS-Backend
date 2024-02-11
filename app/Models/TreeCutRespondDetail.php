<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreeCutRespondDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'distance',
        'tree_height',
        'tree_girth',
        'recommendation',
        'tree_cut_request_details_id',
        'tree_cut_responds_id',
    ];
}
/*
    1 (TreeCutRequest) : M (TreeCutRespondDetails)
    1 (TreeCutRequestDetail) : 1 (TreeCutRespondDetail)
    1 (TreeCutResponnd) : 1 (TreeCutRespondDetail)
*/