<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialSecurityBillingItems extends Model
{
    use HasFactory;

    protected $table = 'social_security_items';
    protected $fillable = [
        'social_security_id',
        'bp_no',
        'crn_no',
        'effectivity_date',
        'ps',
        'gs',
        'ec',
        'consoloan',
        'ecardplus',
        'salary_loan',
        'cash_adv',
        'emrgy_loan',
        'educ_loan',
        'ela',
        'sos',
        'plreg',
        'plopt',
        'rel',
        'lch_dcs',
        'stock_purchase',
        'opt_life',
        'ceap',
        'edu_child',
        'genesis',
        'genplus',
        'genflexi',
        'genspcl',
        'help',
        'gfal',
        'mpl',
        'cpl',
        'gel',
        'mpl_lite'
    ];

    public $timestamps = false;

    public function social_security() {
        return $this->hasOne(SocialSecurityBilling::class, 'id', 'social_security_id');
    }
    
}
