<?php

namespace Database\Seeders;

use App\Models\Sections;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $product = config('app.product');

        if($product == 'government') {
            $data = [
                ['code' => 'GASS', 'name' => 'General and Administrative Services', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'CPAS', 'name' => 'Communications and Public Affairs Service', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'FOSU', 'name' => 'Field Operations Support Unit', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'FMS', 'name' => 'Financial Management Service', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'GPH', 'name' => 'GPH Implementing Panel Secretariat for the GPH-MILF Peace Accord', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'JPSC', 'name' => 'Security Unit - JPSC Secretariat', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'SU', 'name' => 'Security Unit - Combined Secretariat (GPH-CCCH, GPH-AHJAG)', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'HRMS', 'name' => 'Human Resource Management Services', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'IPPO', 'name' => 'International and Private Partnership Office', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'LCT-FISU', 'name' => 'LCT - Field Implementation Support Unit (FISU)', 'department_id' => 3, 'branch_id' => 2],
                ['code' => 'LLS', 'name' => 'Legislative and Legal Service', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'MILF PPO', 'name' => 'MILF Peace Process Office', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'MEALS', 'name' => 'Monitoring, Evaluation, Accountability and Learning Service', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'NORM', 'name' => 'Normalization Core Unit (JNC Secretariat)', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'IDB', 'name' => 'Normalization Core Unit (IDB)', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'OCGPH', 'name' => 'Office of the Chairperson of the GPH Peace Implementing Panel for the GPH-MILF Peace Process', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OCOS', 'name' => 'Office of the Chief of Staff', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OEDBT', 'name' => 'Office of the Executive Director for Bangsamoro Transformation', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OEDPP', 'name' => 'Office of the Executive Director for Plans and Programs', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OEDPS', 'name' => 'Office of the Executive Director for Peace Sustainability', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OPABT', 'name' => 'Office of the Presidential Assistant for Bangsamoro Transformation', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OPAIMC', 'name' => 'Office of the Presidential Assistant for Internal Management Cluster', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OPALCT', 'name' => 'Office of the Presidential Assistant for Local Conflict Transformation', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OEDLCT', 'name' => 'Office of the Executive Director for Local Conflict Transformation', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'ODPAPRU', 'name' => 'Office of the Senior Undersecretary/Deputy PAPRUM', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'OSECGSS', 'name' => 'OSEC General Support Services', 'department_id' => 9, 'branch_id' => 2],
                ['code' => 'PAMANA-NPMO', 'name' => 'PAMANA National Program Management Office', 'department_id' => 6, 'branch_id' => 2],
                ['code' => 'PPPCO', 'name' => 'Peace Panel and Political Concern Office', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'PPRMS', 'name' => 'Planning, Programming and Resource Management Service', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'PSDKMS', 'name' => 'Policy and Strategy Development and Knowledge Management Service', 'department_id' => 8, 'branch_id' => 2],
                ['code' => 'RCPPO', 'name' => 'RPA-CPLA Peace Process Office', 'department_id' => 4, 'branch_id' => 2],
                ['code' => 'CPPO', 'name' => 'CPP-NPA NDFP Peace Process Office', 'department_id' => 3, 'branch_id' => 2],
                ['code' => 'SHAPEO', 'name' => 'Social Healing and Peacebuilding Office', 'department_id' => 5, 'branch_id' => 2],
                ['code' => 'JTFCT', 'name' => 'Socioeconomic Development Office - JTFCT', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'TFDCC', 'name' => 'Socioeconomic Development Unit - TFDCC', 'department_id' => 1, 'branch_id' => 2],
                ['code' => 'TJHRU', 'name' => 'Transitional Justice, Healing and Reconciliation Unit', 'department_id' => 2, 'branch_id' => 2],
                ['code' => 'MNLF PPO', 'name' => 'MNLF Peace Process Office', 'department_id' => 2, 'branch_id' => 2],
            ];
        } else {
            $data = [];
        }            

        foreach ($data as $data) {
            Sections::updateOrCreate(
                ['code' => $data['code'], 'name' => $data['name']],
                $data
            );
        }
    }
}
