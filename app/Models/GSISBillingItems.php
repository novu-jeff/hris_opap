<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GSISBillingItems extends Model
{
    use HasFactory;

    protected $table = 'gsis_billing_items';
    protected $fillable = [
        'gsis_billing_id',
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
    ];

    public $timestamps = false;
    
}
