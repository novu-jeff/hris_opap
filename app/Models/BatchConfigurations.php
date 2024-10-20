<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchConfigurations extends Model
{
    use HasFactory;
    protected $table = 'batch_configurations';
    protected $fillable = [
        'name',
        'batch_id',
        'isActive'
    ];
}
