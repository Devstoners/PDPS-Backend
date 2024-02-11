<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplainAction extends Model
{
    use HasFactory;
    protected $fillable = [
        'complain_id',
        'action',
        'action_date',
    ];
}
/*
    1(Complain) : M (ComplainAction)
*/