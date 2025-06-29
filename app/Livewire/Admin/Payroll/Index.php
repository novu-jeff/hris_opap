<?php

namespace App\Livewire\Admin\Payroll;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Admin\Services\PayrollService;
use App\Models\EmployementTypes;
use App\Models\Payroll;
use App\Notifications\Notifications;
use Livewire\Component;
use Livewire\WithPagination;
use App\Jobs\ProcessPayroll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Bus\Batch;
use Throwable;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $entries = 10;
    public $status = '';
    public $employmentTypes;

    public string $type;
    public string $employment_type;

    public string $payroll_date;
    public string $cut_off_period;
    public string $employment_type_id;
    public array $employeesChecked;
    public string $activeTab;
    public bool $isToCreate = false;

    public string $payroll_id;
    public int $batchProgress = 0;
    public string|null $batchId = null;
    public bool $isBatchProcessing = false;
    public string $batchStatusMessage = 'Please Wait...';
    public $actionBy;

    protected $listeners = ['createPayroll', 'dispatchPayrollJobs', 'cancelPayroll', 'removePayroll'];

    public function mount()
    {
        $this->actionBy = Auth::user();
        $this->employmentTypes = EmployementTypes::all();
    }

    protected function rules()
    {
        return [
            'payroll_date' => 'required|date|unique:payroll,payroll_date',
            'cut_off_period' => [
                'required',
                'unique:payroll,cut_off_period',
                'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
            ],
            'employment_type_id' => 'required|exists:employment_types,id'
        ];
    }

    public function go_back()
    {
        $this->reset([
            'employeesChecked',
            'isToCreate',
            'activeTab'
        ]);

        $this->resetValidation();
    }

    public function setActiveTab($value) {
        $this->activeTab = $value;
    }

    public function createPayroll()
    {
        $this->validate();

        try {
            if (!$this->isToCreate) {
                if (empty($this->employment_type_id)) {
                    throw new \Exception("Employment type is required to fetch employees.");
                }

                $payrollService = new PayrollService();

                $this->employeesChecked = $payrollService->getEmployeesPreview($this->employment_type_id);

                if (empty($this->employeesChecked)) {
                    throw new \Exception("No employees found for the selected employment type.");
                }

                $this->isToCreate = true;
                return;
            }

            if (Payroll::where([
                'payroll_date' => $this->payroll_date,
                'cut_off_period' => $this->cut_off_period,
                'employment_type' => $this->employment_type_id,
            ])->exists()) {
                throw new \Exception("Payroll for this period and employment type already exists.");
            }

            $payroll = Payroll::create([
                'type' => $this->type,
                'payroll_date' => $this->payroll_date,
                'cut_off_period' => $this->cut_off_period,
                'employment_type' => $this->employment_type_id,
                'status' => 'pending'
            ]);

            $this->payroll_id = $payroll->id;

            $this->dispatch('hideModal', [
                'modal' => 'newSalaryPayroll'
            ]);

            $this->dispatch('start-job-dispatch', [
                'payroll_id' => $payroll->id,
                'employment_type' => $payroll->employment_type,
            ]);

            $this->reset([
                'cut_off_period',
                'payroll_date',
                'employment_type_id',
                'employeesChecked',
                'isToCreate'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Validation Failed',
                'message' => 'Please check all required fields.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
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

    public function regeneratePayroll($payroll_id) {

        $payroll = Payroll::findOrFail($payroll_id);
        $this->payroll_id = $payroll_id;

        $this->dispatch('start-job-dispatch', [
            'payroll_id' => $payroll->id,
            'employment_type' => $payroll->employment_type,
        ]);

    }

    public function dispatchPayrollJobs($payroll_id, $employmentType)
    {
        $payroll = Payroll::findOrFail($payroll_id);

        $payrollService = new PayrollService();
        $employees = $payrollService->getEmployees($employmentType);

        $chunks = array_chunk($employees->toArray(), 1000);

        $jobs = [];

        foreach ($chunks as $chunk) {
            $jobs[] = new ProcessPayroll(collect($chunk), $payroll);
        }

        $payroll_date = Carbon::parse($payroll->payroll_date)->format('M d, Y');

        if(!empty($jobs)) {
            $batch = Bus::batch($jobs)
                ->withOption('actionBy', [
                    'id' => $this->actionBy->id,
                    'name' => $this->actionBy->name
                ])
                ->name('Payroll For ' . $payroll_date)
                ->catch(function (Batch $batch, Throwable $e) {
                    $this->actionBy?->notify(new Notifications(
                        'error',
                        'An error occurred during processing the payroll.',
                        route('system.jobs', ['id' => $batch->id]),
                        'admin'
                    ));
                })
                ->then(function (Batch $batch) { 
                    $this->actionBy?->notify(new Notifications(
                        'success',
                        'The processing of payroll has been finished.',
                            route('system.jobs', ['id' => $batch->id]),
                        'admin'
                    ));
                })
                ->dispatch();

            $payroll->update(['batch_id' => $batch->id]);

            $this->batchId = $batch->id;
            $this->isBatchProcessing = true;
        } else {

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops',
                'message' => 'No jobs were processed'
            ]);

        }
    }

    public function checkBatchStatus()
    {
        if (!$this->batchId) return;

        $batch = Bus::findBatch($this->batchId);

        if ($batch) {

            $this->batchProgress = $batch->progress();

            $this->batchStatusMessage = match (true) {
                $this->batchProgress < 10   => 'Retrieving employee records...',
                $this->batchProgress < 60   => 'Calculating salaries, deductions, and benefits...',
                $this->batchProgress < 80   => 'Generating payroll items and inserting records...',
                $this->batchProgress < 95   => 'Finalizing reports...',
                $this->batchProgress <= 100 => 'Redirecting...',
            };

            if ($batch->finished()) {
                $this->dispatch('redirect_to', [
                    'url' => route('payroll.process', ['payroll_id' => Payroll::where('batch_id', $this->batchId)->value('id')]),
                    'delay' => 3000
                ]);
            }
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
        }
    }

    public function deletePayroll($payroll_id) {
        $payroll = Payroll::find($this->payroll_id);

        if ($payroll) {
            $batchId = $payroll->batch_id;

            $payroll->delete();

            if ($batchId) {
                Bus::findBatch($batchId)?->delete();
            }

            $this->reset([
                'isBatchProcessing',
                'batchStatusMessage',
                'batchProgress',
            ]);
        }

        return;
    }

    public function render()
    {
        $mapping = [
            'contractual' => 'contractual',
            'cos' => 'contract of service',
            'jo' => 'job order'
        ];

        $employmentType = null;

        if ($this->employment_type && isset($mapping[$this->employment_type])) {
            $employmentType = EmployementTypes::where('name', $mapping[$this->employment_type])->first();
        }

        $records = Payroll::where('type', $this->type)
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($employmentType, fn($q) => $q->where('employment_type', $employmentType->id))
            ->paginate($this->entries);

        return view('livewire.admin.payroll.index', [
            'records' => $records
        ]);
    }
}
