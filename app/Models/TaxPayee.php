<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPayee extends Model
{
    use HasFactory;
    
    protected $table = 'tax_payees';
    
    protected $fillable = [
        'title',
        'name',
        'nic',
        'tel',
        'address',
        'email',
    ];

    public function taxProperties()
    {
        return $this->hasMany(TaxProperty::class, 'tax_payee_id');
    }

    public function taxAssessments()
    {
        return $this->hasManyThrough(TaxAssessment::class, TaxProperty::class, 'tax_payee_id', 'tax_property_id');
    }

    public function taxPayments()
    {
        return $this->hasManyThrough(TaxPayment::class, TaxProperty::class, 'tax_payee_id', 'tax_property_id');
    }
}
