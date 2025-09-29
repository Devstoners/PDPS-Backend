<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'nic',
        'tel',
        'address',
        'email',
    ];

    public function reservations()
    {
        return $this->hasMany(HallReservation::class);
    }
}
