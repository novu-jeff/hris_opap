<?php

namespace App\Livewire\Employee;

use App\Notifications\Notifications;
use App\Models\EmployeePayslipRequest;
use App\Models\EmployeeAccount;
use App\Models\SalaryItemsPayroll;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Payslip extends Component
{

    public $employee_id;
    public $employee_no;
    public $payroll;
    public $payslip;
    public $error;
    public $requestStatus;
    protected $listeners = ['request'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $this->employee_no = Auth::user()->employee_no;
        $this->employee_id = Auth::user()->id;

        $payroll = SalaryItemsPayroll::with('information.section', 'payroll')
            ->where('employee_no', $this->employee_no)
            ->whereHas('payroll', function($query) {
                return $query->where('status', 'approved');
            })
            ->first();

        if(!$payroll) {
            return $this->error = 'No Payslip Found';
        }

        $this->payroll = $payroll;
        $this->payslip = $payroll;

        $this->checkRequest();

    }

    public function checkRequest() {
        $payroll_request = EmployeePayslipRequest::where('employee_no', $this->employee_no)
            ->where('payroll_id', $this->payroll->payroll_id)
            ->first();
        $this->requestStatus = $payroll_request->status ?? null;
    }

    public function download()
    {
        $this->checkRequest();

        if ($this->requestStatus == 'approved') {

            $payroll_date = Carbon::parse($this->payroll->payroll_date)->format('F d, Y');
            $filename = $this->employee_no . '|Payslip for ' . $payroll_date . '.pdf';

            return $this->dispatch('download-payslip', [
                'allowDownload' => true,
                'filename' => $filename
            ]);
        }

        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Oops!',
            'message' => 'You\'re request is not yet approved. You have no permission to download this payslip.',
        ]);

    }


    public function changePeriod($control, $direction) {
        
        $currentDate = $this->payroll->payroll->payroll_date ?? null;
        $employeeNo = $this->employee_no;

        if (!$currentDate) {
            return $this->error = 'Current payroll date not available';
        }

        $query = SalaryItemsPayroll::with('information.section', 'payroll')
            ->where('employee_no', $employeeNo)
            ->whereHas('payroll', function ($q) use ($currentDate, $direction) {
                $q->where('status', 'approved');

                if ($direction == '-1') {
                    $q->where('payroll_date', '<', $currentDate)->orderBy('payroll_date', 'desc');
                } else {
                    $q->where('payroll_date', '>', $currentDate)->orderBy('payroll_date', 'asc');
                }
            });

        $nextPayroll = $query->first();

        if ($nextPayroll) {
            $this->payroll = $nextPayroll;
            $this->payslip = $nextPayroll;
        } else {
            $this->error = 'No more payroll records in this direction.';
        }
    }

    public function request(bool $isNotify = true) {
        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'You\'re about to send a request for payslip download.';
            $action = 'request';
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        } else {

            DB::beginTransaction();

            try {

                EmployeePayslipRequest::create([
                    'payroll_id' => $this->payroll->payroll_id,
                    'employee_no' => $this->employee_no,
                    'status' => 'pending'
                ]);
              
                $user = EmployeeAccount::find($this->employee_id);
                $message = "Employee <strong>{$this->employee_no}</strong> submitted a request for payslip download.";
                $user->notify(new Notifications('info', $message, route('ess.payslip-request'), 'admin'));

                DB::commit();

                $this->requestStatus = 'pending';

                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'success',
                    'title' => 'Yey!',
                    'message' => 'Your application has been successfully submitted.',
                    'redirect' => '_reload'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops',
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }

        }
    }

    public function render()
    {
        return view('livewire.employee.payslip');
    }
}
