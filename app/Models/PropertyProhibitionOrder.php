<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyProhibitionOrder extends Model
{
    use HasFactory;
    
    protected $table = 'property_prohibition_orders';
    
    protected $fillable = [
        'tax_property_id',
        'order_date',
        'revoked_date',
        'status',
        'officer_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'revoked_date' => 'date',
    ];

    public function taxProperty()
    {
        return $this->belongsTo(TaxProperty::class, 'tax_property_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
