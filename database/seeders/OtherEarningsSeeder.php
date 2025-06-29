<?php

namespace Database\Seeders;

use App\Models\OtherEarnings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OtherEarningsSeeder extends Seeder
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
                    'code' => 'PERA',
                    'name' => 'Personal Economic Relief Allowance',
                    'amount_basis' => 'entry',
                    'amount' => 2000,
                    'frequency_basis' => 'monthly',
                    'frequency' => null,
                    'eligible' => 1,
                    'isTaxable' => true,
                    'forcasted' => 0,
                ],
                [
                    'code' => 'Clothing',
                    'name' => 'Clothing Allowance',
                    'amount_basis' => 'entry',
                    'amount' => 7000,
                    'frequency_basis' => 'yearly',
                    'frequency' => null,
                    'eligible' => 1,
                    'isTaxable' => false,
                    'forcasted' => 0,
                ],
                [
                    'code' => 'MID',
                    'name' => 'Mid Year Bonus',
                    'amount_basis' => 'basic_salary',
                    'amount' => 1, 
                    'frequency_basis' => 'yearly',
                    'frequency' => null,
                    'eligible' => 1,
                    'isTaxable' => true,
                    'forcasted' => 180, 
                ],
                [
                    'code' => 'Yearend',
                    'name' => 'Year End Bonus',
                    'amount_basis' => 'basic_salary',
                    'amount' => 1, 
                    'frequency_basis' => 'yearly',
                    'frequency' => null,
                    'eligible' => 1,
                    'isTaxable' => true,
                    'forcasted' => 120, 
                ],
                [
                    'code' => 'CASHGIFT',
                    'name' => 'Cash Gift',
                    'amount_basis' => 'entry',
                    'amount' => 5000,
                    'frequency_basis' => 'yearly',
                    'frequency' => null,
                    'eligible' => 1,
                    'isTaxable' => true,
                    'forcasted' => 0,
                ],
                [
                    'code' => 'Premium',
                    'name' => 'Premium Pay',
                    'amount_basis' => 'percentage',
                    'amount' => 5, 
                    'frequency_basis' => 'month_picked',
                    'frequency' => 'June, December',
                    'eligible' => 2, 
                    'isTaxable' => true,
                    'forcasted' => 0,
                ],
            ];
        } else {
            $data = [];
        }            

        if(!empty($data)) {
            foreach ($data as $data) {
                OtherEarnings::updateOrCreate(
                    ['code' => $data['code'], 'name' => $data['name']],
                    $data
                );
            }
        }
    }
}
