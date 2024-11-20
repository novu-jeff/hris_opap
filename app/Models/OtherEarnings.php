<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherEarnings extends Model
{
    use HasFactory;

    protected $table = 'other_earnings';
    protected $fillable = [
        'code',
        'name',
        'amount_basis',
        'amount',
        'frequency',
        'month_frequency',
        'eligible',
        'isTaxable',
        'forcasted',
    ];

}
