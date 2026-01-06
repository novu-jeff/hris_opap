<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrancheItems extends Model
{
    use HasFactory;

    protected $table = 'tranche_items';
    protected $fillable = [
         'tranche_id',
        'salary_grade',
        'step_1',
        'step_1_wtax',
        'step_2',
        'step_2_wtax',
        'step_3',
        'step_3_wtax',
        'step_4',
        'step_4_wtax',
        'step_5',
        'step_5_wtax',
        'step_6',
        'step_6_wtax',
        'step_7',
        'step_7_wtax',
        'step_8',
        'step_8_wtax',
    ];
}
