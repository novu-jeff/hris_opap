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

        $product = config('app.product');

        if($product == 'government') {
             $data = [
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
                    'code' => 'Unliquidated_Cash_Advances',
                    'name' => 'Unliquidated Cash Advances',
                    'frequency' => 'bi_monthly',
                    'month_frequency' => null,
                    'source' => 'entry',
                    'eligible' => 1,
                ],
                [
                    'code' => 'PHILHEALTH',
                    'name' => 'PHILHEALTH',
                    'frequency' => 'monthly',
                    'month_frequency' => null,
                    'source' => 'entry',
                    'eligible' => 1,
                ],
                [
                    'code' => 'HDMF',
                    'name' => 'HDMF',
                    'frequency' => 'monthly',
                    'month_frequency' => null,
                    'source' => 'entry',
                    'eligible' => 1,
                ],
                [
                    'code' => 'MP2',
                    'name' => 'MP2',
                    'frequency' => 'monthly',
                    'month_frequency' => null,
                    'source' => 'entry',
                    'eligible' => 1,
                ],
                [
                    'code' => 'MPLSTLMS',
                    'name' => 'MPL STLMS',
                    'frequency' => 'monthly',
                    'month_frequency' => null,
                    'source' => 'entry',
                    'eligible' => 1,
                ],
                [
                    'code' => 'CIR375, CIR449',
                    'name' => 'CIR375, CIR449',
                    'frequency' => 'monthly',
                    'month_frequency' => null,
                    'source' => 'entry',
                    'eligible' => 1,
                ],
            ];
        } else {
            $data = [];
        }

        if(!empty($data)) {
            foreach ($data as $data) {
                OtherDeductions::updateOrCreate(
                    ['code' => $data['code'], 'name' => $data['name']],
                    $data
                );
            }
        }
    }
}
