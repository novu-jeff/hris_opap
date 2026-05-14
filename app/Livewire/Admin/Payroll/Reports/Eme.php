<?php

namespace App\Livewire\Admin\Payroll\Reports;

use App\Models\EmployementTypes;
use App\Models\PayrollEme;
use Illuminate\Support\Facades\Bus;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Eme extends Component
{
    public $payroll_id, $status, $entries, $type, $employment_type;
    public $records;
    protected $listeners = ['regeneratePayroll', 'removePayroll', 'cancelPayroll'];

    public function regeneratePayroll($payroll_id) {

      
       // dd('here');
        // 1️⃣ Fetch the payroll record
        $payroll = PayrollEme::findOrFail($payroll_id);
        //dd($payroll);
        $this->payroll_id = $payroll_id;

        // 2️⃣ Delete existing payroll items for this payroll
        \DB::table('payroll_eme_items')
            ->where('payroll_id', $payroll->id)
            ->delete();

        // 2️⃣ Get your PayrollService
        $service = app(\App\Http\Controllers\Admin\Services\PayrollService::class);

        // 3️⃣ Fetch employees for this payroll
        // getEmployees() returns eligible/ineligible arrays
     
        $employeesData = $service->getEmployees($payroll->employment_type, 'eme_rata');

        //dd($employeesData);
      

        // Only take eligible employees
        $employees = $employeesData['eligible']['items'] ?? [];

        $selectedEmployees = json_decode($payroll->selected_employees ?? '[]', true);
        if (!empty($selectedEmployees)) {
            $employees = collect($employees)
                ->filter(function ($employee) use ($selectedEmployees) {
                    return in_array($employee['employee_no'], $selectedEmployees);
                })
                ->values()
                ->toArray();
        }

        if (empty($employees)) {
            session()->flash('error', 'No eligible employees found for this payroll.');
            return;
        }
//dd($employees);
        // 4️⃣ Split employees into manageable chunks
        $chunks = array_chunk($employees, 25);

       // dd($chunks );

        // 5️⃣ Prepare PayrollJob instances
        $jobs = [];
        foreach ($chunks as $chunk) {
            Log::info('regenrate chuck data', ['chuck' => $chunk]);
            $jobs[] = new \App\Jobs\PayrollJob(
                $chunk,        // array of employees
                $payroll->id,  // payroll ID
                'eme'       // payroll type
            );
        }
   \Log::channel('payroll')->info('Payroll batch prepared', [
       'payroll_id' => $payroll->id,
       'type' => 'eme',
       'jobs_count' => count($jobs),
   ]);
        // 6️⃣ Dispatch jobs as a batch
        $batch = \Illuminate\Support\Facades\Bus::batch($jobs)
            ->name('Regenerate Payroll #' . $payroll->id)
            ->allowFailures()
            ->dispatch();

        // 7️⃣ Save batch ID in payroll record
        $payroll->batch_id = $batch->id;
        $payroll->save();

       

        $this->dispatch('start-job-dispatch', [
            'payroll_id' => $payroll->id,
            'employment_type' => $payroll->employment_type,
            'type' => 'eme_rata'
        ]);

    }

    public function cancelPayroll(bool $isNotify = true)
    {
        
        if($isNotify) {
            $title = 'Are you sure to cancel the current process?';
            $message = 'Please be informed that by proceeding, the entire process finished will be undone and removed';
            $action = 'cancelPayroll';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action,
            ]);
        } else {
            $this->deletePayroll($this->payroll_id);
        }        
    }

    public function removePayroll(bool $isNotify, int $payroll_id = null)
    {
        if($isNotify) {
            $title = 'Are you sure to remove this payroll?';
            $message = 'Please be informed that by proceeding, all data that is connected to this payroll process will be permanently deleted.';
            $action = 'removePayroll';

            $this->payroll_id = $payroll_id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action,
            ]);

            

        } else {
            $this->deletePayroll($this->payroll_id);

            // ✅ Dispatch success AFTER deletion
        $this->dispatch('alert', [
            'status' => 'success',
            'title' => 'Success!',
            'id' => $this->payroll_id,
            'isRemoveRowDT' => true,
            'message' => 'Payroll #' . strtoupper($this->payroll_id) . ' has been successfully removed.'
        ]);
        }
    }

    public function deletePayroll($payroll_id) {
        $payroll = PayrollEme::find($payroll_id);

        if ($payroll) {
            $batchId = $payroll->batch_id;

            $payroll->delete();

            if ($batchId) {
                Bus::findBatch($batchId)?->delete();
            }
        }

        return;
    }


    public function render()
    {
        $employment_type_id = EmployementTypes::where('name', 'like', '%' . $this->employment_type . '%')->value('id');
        
        $records = PayrollEme::where('employment_type', $employment_type_id)
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('payroll_date', 'desc')
            ->paginate($this->entries);

        // ✅ GROUP ONLY CURRENT PAGE
        $grouped = $records->getCollection()->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->payroll_date)->format('F Y');
        });

        return view('livewire.admin.payroll.reports.eme', [
            'salary' => $records,
            'groupedSalary' => $grouped
        ]);
    }
}
