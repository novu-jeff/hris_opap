<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tranche;
use App\Models\TrancheItem;
use App\Models\Position;
use App\Models\Positions;
use App\Models\TrancheItems;
use App\Models\EmployeeInformation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RunInit extends Command
{
    protected $signature = 'run:init';
    protected $description = '';

    public function handle()
    {
        $this->setDefaultHDMF();

        return Command::SUCCESS;
    }

    protected function setDefaultHDMF() {

        $product = config('app.product');

        if($product == 'government') {

            DB::table('employee_deduction')->truncate();

            $employees = DB::table('employee_information')
                ->select(DB::raw('employee_no'))
                ->pluck('employee_no');

            foreach($employees as $employee) {
                DB::table('employee_deduction')->insert([
                    'employee_no'=> $employee,
                    'deduction_id' => 5,
                    'amount' => 200,
                    'as_of' => Carbon::now()->format('Y-m-d')
                ]);
            }

        }

        $this->info("HDMF deductions set to default");

    }

}
