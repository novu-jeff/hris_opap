<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GSISBilling extends Model
{
    use HasFactory;

    protected $table = 'gsis_billing';
    protected $fillable = [
        'remitting_agency',
        'office_code',
        'billing_month',
        'date_uploaded'
    ];

    public function items() {
        return $this->hasMany(GSISBillingItems::class, 'gsis_billing_id');
    }

}
