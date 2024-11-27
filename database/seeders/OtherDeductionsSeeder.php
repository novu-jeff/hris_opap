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
        $other_earnings = [
            [
                'code' => 'GSIS',
                'name' => 'GSIS Contribution',
                'frequency' => 'monthly',
                'month_frequency' => null,
                'source' => 'file_upload',
                'eligible' => 1,
            ],
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

        foreach ($other_earnings as $earning) {
            OtherDeductions::updateOrCreate(
                ['code' => $earning['code'], 'name' => $earning['name']],
                $earning
            );
        }
    }
}
