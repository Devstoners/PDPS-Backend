<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreeCutRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'requester_name',
        'requester_address',
        'requester_tel',
        'owner_name',
        'owner_address',
        'road',
        'grama_divisions_id',
    ];
}
/*
    1 (TreeCutRequest) : M (TreeCutRequestDetail)
    1 (TreeCutRequest) : 1 (TreeCutRespond)
*/