<?php

namespace App\Livewire\Admin\Payroll;

use App\Models\EmployeeBusinessSlip;
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

    public $payroll_date;
    public $cut_off_period = '';

    protected function rules() {
        return [
            'payroll_date' => 'required|date|unique:payroll,payroll_date',
            'cut_off_period' => [
                'required',
                'unique:payroll,cut_off_period',
                'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/'
            ]
        ];
    }
    
    
    public function createPayroll() {

        $this->validate();

        try {

            $payroll = Payroll::create([
                'payroll_date' => $this->payroll_date,
                'cut_off_period' => $this->cut_off_period,
                'status' => 'ongoing'
            ]);

            return redirect()
                ->route('payroll.choose', ['payroll_id' => $payroll->id]);

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
