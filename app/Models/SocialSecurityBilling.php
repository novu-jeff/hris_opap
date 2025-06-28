<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialSecurityBilling extends Model
{
    use HasFactory;

    protected $table = 'social_security';
    protected $fillable = [
        'remitting_agency',
        'office_code',
        'billing_month',
        'date_uploaded'
    ];

    public function items() {
        return $this->hasMany(SocialSecurityBillingItems::class, 'social_security_id');
    }

}
