<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'max_multiplier', 'allow_multiple', 'default_term', 'is_active'
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
