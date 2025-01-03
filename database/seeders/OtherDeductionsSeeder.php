<?php

namespace Database\Seeders;

use App\Models\OtherDeductions;
use App\Models\OtherEarnings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OtherDeductionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $other_deductions = [
            [
                'code' => 'DBP Savings',
                'name' => 'DBP Savings',
                'frequency' => 'monthly',
                'month_frequency' => null,
                'source' => 'entry',
                'eligible' => 1,
            ],
            [
                'code' => 'Unlad Kawani',
                'name' => 'Unlad Kawani',
                'frequency' => 'monthly',
                'month_frequency' => null,
                'source' => 'entry',
                'eligible' => 1,
            ],
            [
                'code' => 'PAGIBIG_LOANS',
                'name' => 'PAGIBIG LOANS',
                'frequency' => 'monthly',
                'month_frequency' => null,
                'source' => 'entry',
                'eligible' => 1,
            ],
            [
                'code' => 'Unliquidated_Cash_Advances',
                'name' => 'Unliquidated Cash Advances',
                'frequency' => 'bi_monthly',
                'month_frequency' => null,
                'source' => 'entry',
                'eligible' => 1,
            ],
        ];

        foreach ($other_deductions as $deductions) {
            OtherDeductions::updateOrCreate(
                ['code' => $deductions['code'], 'name' => $deductions['name']],
                $deductions
            );
        }
    }
}
