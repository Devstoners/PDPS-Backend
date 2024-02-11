<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreeCutRequestDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'tree_type',
        'tree_count',
        'tree_cut_requests_id',
    ];
}
/*
    1 (TreeCutRequest) : M (TreeCutRequestDetail)
*/