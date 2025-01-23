<?php

namespace Database\Seeders;

use App\Models\CompanyInformation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $product = config('app.product');

        if($product == 'opap') {
            $information = [
                'name' => 'Office of the Presidential Adviser on Peace, Reconciliation and Unity', 
                'address' => 'Agusting 1 Bldg., F, Ortigas Jr. Road, Ortigas Center, Pasig, Metro Manila',
                'contact' => '8636-0707',
                'type_id' => 10
            ];
        } else if ($product == 'novu' || $product == 'testing') {
            $information = [
                'name' => 'Novulutions Inc.', 
                'address' => 'EcoTower, 32nd St. Cor, 9th Ave, Taguig, Metro Manila, Philippines',
                'contact' => '',
                'type_id' => 5
            ];
        }

        $company = CompanyInformation::where('id', 1)->first();

        if ($company) {
            $company->update($information);
        } else {
            CompanyInformation::create($information);
        }

    }
}
