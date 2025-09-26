<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;
    protected $table = 'gallery_images';
    protected $fillable = [
        'gallery_id',
        'image_path',
        'order',
    ];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id');
    }

    /**
     * Get the full URL for the image
     */
    public function getFullUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
/*
    M(GalleryImage) : 1(Gallery)
*/
