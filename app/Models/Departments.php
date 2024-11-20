<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $fillable = [
        'name',
        'cost_center_id',
        'code',
        'isActive'
    ];

    public function cost_center() {
        return $this->hasOne(CostCenters::class, 'id', 'cost_center_id');
    }
}
