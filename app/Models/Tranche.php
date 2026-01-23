<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tranche extends Model
{
    use HasFactory;

    protected $table = 'tranche';

    protected $casts = [
        'is_active' => 'boolean',
    ];
    protected $fillable = [
        'name',
        'eligible',
        'year',
        'is_active',
        'isDeleted'
    ];

    public function items() {
        return $this->hasMany(TrancheItems::class, 'tranche_id', 'id');
    }

}
