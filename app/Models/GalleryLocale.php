<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryLocale extends Model
{
    use HasFactory;
    protected $fillable = [
        'album_desc_si',
        'album_desc_en',
        'album_desc_ta',
    ];
}
/*
    1(GalleryLocale) : M (Gallery)
*/