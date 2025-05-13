<?php

namespace App\Livewire\Admin\Payroll;

use App\Models\EmployeeBusinessSlip;
use App\Models\EmployementTypes;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $status = '';
    public $employmentTypes;

    public $payroll_date;
    public $cut_off_period;
    public $employment_type;

    protected function rules() {
        return [
            'payroll_date' => 'required|date|unique:payroll,payroll_date',
            'cut_off_period' => [
                'required',
                'unique:payroll,cut_off_period',
                'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
            ],
            'employment_type' => 'required|exists:employment_types,id'
        ];
    }
    
    public function mount() {
        $this->employmentTypes = EmployementTypes::all();
    }
    
    public function createPayroll() {

        $this->validate();

        try {

            $payroll = Payroll::create([
                'payroll_date' => $this->payroll_date,
                'cut_off_period' => $this->cut_off_period,
                'employment_type' => $this->employment_type,
                'status' => 'pending'
            ]);

            return redirect()
                ->route('payroll.process', ['payroll_id' => $payroll->id]);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Error: ' . $e->getMessage()
            ]);

        }

    }

    public function render() {
        
        $records = Payroll::paginate($this->entries);

        return view('livewire.admin.payroll.index', [
            'records' => $records
        ]);
    }


}
