<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxProperty extends Model
{
    use HasFactory;
    protected $table = 'tax_properties';
    protected $fillable = [
        'division_id',
        'tax_payee_id',
        'street',
        'property_type',
        'property_name',
        'property_prohibition',//0 = No (db default), 1 = yes
    ];
    public function division()
    {
        return $this->belongsTo(Division::class,'division_id');
    }

    public function taxPayee()
    {
        return $this->belongsTo(TaxPayee::class,'tax_payee_id');
    }

    public function taxAssessments()
    {
        return $this->hasMany(TaxAssessment::class, 'tax_property_id');
    }

    public function taxPayments()
    {
        return $this->hasMany(TaxPayment::class, 'tax_property_id');
    }

    public function prohibitionOrders()
    {
        return $this->hasMany(PropertyProhibitionOrder::class, 'tax_property_id');
    }

    public function getActiveProhibitionOrderAttribute()
    {
        return $this->prohibitionOrders()->where('status', 'active')->first();
    }
}
