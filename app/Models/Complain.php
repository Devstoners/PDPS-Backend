<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use HasFactory;
    protected $table = 'complains';

    protected $fillable = [
        'cname',
        'tele',
        'complain',
        'complain_date',
        'img1',
        'img2',
        'img3',
    ];
}
/*
    1(Complain) : M (ComplainAction)
*/
