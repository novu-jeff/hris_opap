<?php



namespace App\Livewire\Employee;

use App\Notifications\Notifications;
use App\Models\EmployeePayslipRequest;
use App\Models\EmployeeAccount;
use App\Models\SalaryItemsPayroll;
use App\Models\SalaryPayroll;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use App\Models\Positions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;

class Payslip extends Component
{

    public $employee_id;
    public $employee_no;
    public $payroll;
    public $payslip;
    public $error;
    public $requestStatus;
    public $currentPeriod;
    public $payslipView = [];

    public $hasPrevious = false;
    public $hasNext = false;

    protected $listeners = ['request'];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {

        $this->employee_no = Auth::user()->employee_no;
        $this->employee_id = Auth::user()->id;

        // Load LATEST approved payroll for this employee
        $payroll = SalaryItemsPayroll::with('information.section', 'payroll','deductions.loan.loanType')
            ->where('employee_no', $this->employee_no)
            ->whereHas('payroll', function($query) {
                $query->where('status', 'approved');
                 $this->secondHalfFilter($query);    
            })
            ->orderBy(
                SalaryPayroll::select('payroll_date')
                    ->whereColumn('payroll_salary.id', 'payroll_salary_items.payroll_id'),
                'desc'
            )
            ->first();

        if (!$payroll) {
            $this->error = 'No Payslip Found';
            return;
        }

        $viewData = $this->buildPayslipViewData($payroll);

        $this->payroll = $payroll;                 // Salary items
        $this->payslip = $payroll; 
        $this->payslipView = $viewData;                // Salary items
        $this->currentPeriod = $payroll->payroll;  // Payroll header

        $this->checkRequest();

        $this->updateNavigationAvailability();

    }

    public function checkRequest() {
        $payroll_request = EmployeePayslipRequest::where('employee_no', $this->employee_no)
            ->where('payroll_id', $this->payroll->payroll_id)
            ->first();
        $this->requestStatus = $payroll_request->status ?? null;
    }

    private function buildPayslipViewData($payslip)
{
    [$start] = explode(' to ', $payslip->payroll->cut_off_period);
    $startDate = Carbon::parse($start);

    return [
        'fullMonthCutoff' =>
            $startDate->copy()->startOfMonth()->format('F j')
            . ' – ' .
            $startDate->copy()->endOfMonth()->format('F j, Y'),

        'monthLabel' => $startDate->format('F Y'),
    ];
}

    public function getEmployeeByPosition($positionName)
    {
        $supervisingOfficer = EmployeeInformation::join('employee_personal as ep', 'employee_information.employee_no', '=', 'ep.employee_no')
        ->join('positions as p', 'employee_information.position_id', '=', 'p.id')
        ->where('p.name', $positionName)
        ->selectRaw("p.name as pname, ep.firstname, ep.middlename, ep.lastname, ep.suffix, CONCAT(ep.firstname, ' ', IFNULL(ep.middlename,''), ' ', ep.lastname, ' ', IFNULL(ep.suffix,'')) as full_name")
        ->first();

        if ($supervisingOfficer) {
            return [
                'full_name' => $supervisingOfficer->full_name,
                'position_name' => $supervisingOfficer->pname
            ];
        }

        return [
            'full_name' => 'N/A',
            'position_name' => 'N/A'
        ];
    }


   

    public function download()
    {
        $this->checkRequest();

        if ($this->requestStatus == 'approved') {

            $payroll_date = Carbon::parse($this->payroll->payroll_date)->format('F d, Y');
            $filename = $this->employee_no . '|Payslip for ' . $payroll_date . '.pdf';

            $supervisingOfficer = $this->getEmployeeByPosition('Chief Administrative Officer');

            $pdf = Pdf::loadView('employee.payslip-pdf', [
                'payslip' => $this->payroll,
                'payslipView' => $this->payslipView,
                'supervisingOfficer' => $supervisingOfficer,
            ]);

            return response()->streamDownload(
                fn() => print($pdf->output()),
                $filename
            );
        }

        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Oops!',
            'message' => 'Your request is not yet approved. You have no permission to download this payslip.',
        ]);
    }

    public function downloadDirect()
    {
        $payroll = SalaryItemsPayroll::with(
            'information.section',
            'payroll',
            'deductions.loan.loanType'
        )
        ->where('employee_no', $this->employee_no)
        ->where('payroll_id', $this->payroll->payroll_id)
        ->first();

        if (!$payroll) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Payslip not found.',
            ]);
        }

        $payslipView = $this->buildPayslipViewData($payroll);

        $supervisingOfficer = $this->getEmployeeByPosition(
            'Chief Administrative Officer'
        );

        $payrollDate = \Carbon\Carbon::parse(
            $payroll->payroll->payroll_date
        )->format('F d, Y');

        $filename = $this->employee_no .
            ' | Payslip for ' .
            $payrollDate .
            '.pdf';

        $pdf = Pdf::loadView('admin.payslip-pdf', [
            'payslip' => $payroll,
            'payslipView' => $payslipView,
            'supervisingOfficer' => $supervisingOfficer,
            'use_employee' => 1,
        ]);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }




    public function changePeriod($control, $direction)
{
    $currentMonth = Carbon::parse($this->currentPeriod->payroll_date)->startOfMonth();
    $employeeNo = $this->employee_no;

    // Determine target month
    $targetMonth = $direction == '-1'
        ? $currentMonth->copy()->subMonth()
        : $currentMonth->copy()->addMonth();

    $next = SalaryItemsPayroll::with('information.section', 'payroll','deductions.loan.loanType')
        ->where('employee_no', $employeeNo)
        ->whereHas('payroll', function ($q) use ($targetMonth) {
            $q->where('status', 'approved');

            // SAME MONTH only
            $q->whereMonth('payroll_date', $targetMonth->month)
              ->whereYear('payroll_date', $targetMonth->year);

            // ONLY 16–30/31 payroll
            $this->secondHalfFilter($q);
        })
        ->first();

    if (!$next) {
        $this->error = 'No more payroll records in this direction.';
        return;
    }

    // Update state
    $this->payroll = $next;
    $this->payslip = $next;
    $this->currentPeriod = $next->payroll;

    $this->payslipView = $this->buildPayslipViewData($next);

    $this->checkRequest();

    $this->updateNavigationAvailability();
}






    public function changePeriodBK($control, $direction) {
        
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

    private function secondHalfFilter($query)
    {
        return $query->whereRaw(
            "DAY(SUBSTRING_INDEX(cut_off_period, ' to ', 1)) = 16"
        );
    }

    private function updateNavigationAvailability()
    {
        $employeeNo = $this->employee_no;
        $currentMonth = Carbon::parse($this->currentPeriod->payroll_date)->startOfMonth();

        // Previous
        $this->hasPrevious = SalaryItemsPayroll::where('employee_no', $employeeNo)
            ->whereHas('payroll', function ($q) use ($currentMonth) {
                $q->where('status', 'approved')
                ->whereMonth('payroll_date', $currentMonth->copy()->subMonth()->month)
                ->whereYear('payroll_date', $currentMonth->copy()->subMonth()->year);
            })
            ->exists();

        // Next
        $this->hasNext = SalaryItemsPayroll::where('employee_no', $employeeNo)
            ->whereHas('payroll', function ($q) use ($currentMonth) {
                $q->where('status', 'approved')
                ->whereMonth('payroll_date', $currentMonth->copy()->addMonth()->month)
                ->whereYear('payroll_date', $currentMonth->copy()->addMonth()->year);
            })
            ->exists();
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
        $supervisingOfficer = $this->getEmployeeByPosition('Chief Administrative Officer');

     

        return view('livewire.employee.payslip', [
            'supervisingOfficer' => $supervisingOfficer,
        ]);

        //return view('livewire.employee.payslip');
    }
}
