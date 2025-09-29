<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title', // 1 = Mr, 2 = Mrs, 3 = Miss, 4 = Rev
        'name_en',
        'name_si',
        'name_ta',
        'image',
        'tel',
        'company_name',
        'company_reg_no',
        'address',
        'supply_category',
        'contact_person',
        'contact_tel',
        'contact_email',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class, 'supplier_id');
    }
}
