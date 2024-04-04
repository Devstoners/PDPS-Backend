<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterCustomer extends Model
{
    use HasFactory;
    protected $table = 'water_customers';
    protected $fillable = [
        'title',
        'name',
        'nic',
        'tel',
        'address',
        'email',
        'con_date',
        'water_schemes_id',
    ];
    public function waterScheme()
    {
        return $this->belongsTo(WaterScheme::class,'water_schemes_id');
    }

    public function waterBills()
    {
        return $this->hasMany(WaterBill::class, 'water_customer_id');
    }
}
