<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterBill extends Model
{
    use HasFactory;
    protected $table = 'water_bills';
    protected $fillable = [
        'water_customer_id',
        'officer_id',
        'year',
        'month',
        'meter_read_date',
        'meter_read_val',
        'outstand_amount',//use '-' and '+'. + is there to handle extra payment
        'bill_amount',
        'pay_date',
        'pay_method',//1=online, 2=cash
        'pay_amount',
    ];
    public function waterCustomer()
    {
        return $this->belongsTo(WaterCustomer::class,'water_customer_id');
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class,'officer_id');
    }
}
