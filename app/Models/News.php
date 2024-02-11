<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $table = 'news';
    protected $fillable = [
        'news_si',
        'news_en',
        'news_ta',
        'visibility',
        'priority',
    ];
//    public function newsLocales()
//    {
//        return $this->hasMany(News_locales::class, 'news_id');
//    }
}
