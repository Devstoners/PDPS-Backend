<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $table = 'galleries';
    protected $fillable = [
        'topic_en',
        'topic_si',
        'topic_ta',
    ];

    public function images()
    {
        return $this->hasMany(GalleryImage::class, 'gallery_id');
    }
}
/*
    m(GalleryImage) : 1 (Gallery)
*/
