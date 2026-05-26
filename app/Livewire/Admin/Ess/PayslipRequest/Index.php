<?php

namespace App\Livewire\Admin\Ess\PayslipRequest;

use App\Models\EmployeeAccount;
use App\Models\EmployeePayslipRequest;
use App\Models\SalaryItemsPayroll;
use App\Models\SalaryPayroll;
use App\Models\EmployeeInformation;
use App\Notifications\Notifications;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class Index extends Component
{
    use WithPagination;

    public $status;
    public $view_records;
    public $selected_id;
    public $activeTab = 'pending';
    protected $listeners = ['remove', 'disapproved', 'approved'];
    public $accepts_autwopay;

    public $payroll;
    public $payslip;
    public $error;
    public $currentPeriod;
    public $payslipView = [];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';
    public $requestStatus;

    // Load a single request to view modal
    public function view(int $id) {
        $this->selected_id = $id;
        $this->loadRecords($id);
        if(!is_null($this->view_records)) {
            return $this->dispatch('showModal', [
                'modal' => 'showModal',
            ]);
        }
    }

    public function loadRecords(int $id) {
        $view_records = EmployeePayslipRequest::with('payroll', 'employee')->where('id', $id)
            ->first();
        $this->view_records = $view_records;
    }

    // Disapprove a request
    public function disapproved(bool $isNotify = true) {
        $this->loadRecords($this->selected_id);

        if($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'You are about to disapprove this request <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>.',
                'action' => 'disapproved'
            ]);
        } else {
            $record = EmployeePayslipRequest::where('id', $this->selected_id)->where('status', 'pending')->first();
            if (!$record) return;

            $record->status = 'disapproved';
            $record->action_by_id = Auth::user()->id;
            $record->save();

            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications(
                'error',
                'Your payslip request <strong>#' . format_id($record->id, 6) . '</strong> was <strong>DISAPPROVED</strong>.',
                route('employee.leave'),
                'employee'
            ));

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success',
                'isRemoveRowDT' => true,
                'message' => 'Request has been disapproved'
            ]);
        }
    }

    // Approve a request
    public function approved(bool $isNotify = true) {
        $this->loadRecords($this->selected_id);

        if($isNotify) {
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'You are about to approve this payslip request <b>#' . strtoupper(format_id($this->selected_id, 6)) . '</b>.',
                'action' => 'approved'
            ]);
        } else {
            $record = EmployeePayslipRequest::where('id', $this->selected_id)->where('status', 'pending')->first();
            if (!$record) return redirect()->route('ess.payslip-request');

            $record->update([
                'action_by_id' => Auth::user()->id,
                'status' => 'approved'
            ]);

            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications(
                'success',
                'Your request <strong>#' . format_id($record->id, 6) . '</strong> for payslip download was <strong>APPROVED</strong>.',
                route('employee.payslip'),
                'employee'
            ));

            $this->dispatch('alert', [
                'id' => $this->selected_id,
                'showAlert' => true,
                'status' => 'success',
                'title' => 'Success',
                'isRemoveRowDT' => true,
                'message' => 'Request has been approved'
            ]);
        }
    }

    // Remove a request
    public function remove(bool $isNotify = true, ?int $id = null) {
        if($isNotify) {
            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => 'Are you sure to continue?',
                'message' => 'You are about to delete request <b>#' . strtoupper(format_id($id, 6)) . '</b>.',
                'action' => 'remove'
            ]);
        } else {
            $record = EmployeePayslipRequest::find($this->selected_id);
            if (!$record) return;

            $record->isDeleted = true;
            $record->action_by_id = Auth::user()->id;
            $record->save();

            $user = EmployeeAccount::where('employee_no', $record->employee_no)->first();
            $user?->notify(new Notifications(
                'error',
                'Your request <strong>#' . format_id($record->id, 6) . '</strong> was <strong>REMOVED</strong>.',
                route('employee.leave'),
                'employee'
            ));

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'id' => $this->selected_id,
                'isRemoveRowDT' => true,
                'message' => 'Payslip request deleted successfully.'
            ]);
        }
    }

    // Build Payslip view data
    private function buildPayslipViewData($payslip) {
        [$start] = explode(' to ', $payslip->payroll->cut_off_period);
        $startDate = Carbon::parse($start);

        return [
            'fullMonthCutoff' => $startDate->copy()->startOfMonth()->format('F j') . ' – ' . $startDate->copy()->endOfMonth()->format('F j, Y'),
            'monthLabel' => $startDate->format('F Y'),
        ];
    }

    // Download payslip
    public function download(int $requestId)
    {
        $request = EmployeePayslipRequest::with('payroll')->where('id', $requestId)->where('status', 'approved')->first();
        if (!$request) {
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Oops!',
                'message' => 'Request not approved or does not exist.',
            ]);
        }

        $employeeNo = $request->employee_no;
        $payrollId  = $request->payroll_id;

        $payroll = SalaryItemsPayroll::with('information.section', 'payroll','deductions.loan.loanType')
            ->where('employee_no', $employeeNo)
            ->where('payroll_id', $payrollId)
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
        $supervisingOfficer = $this->getEmployeeByPosition('Supervising Administrative Officer');

        $payrollDate = Carbon::parse($payroll->payroll->payroll_date)->format('F d, Y');
        $filename = $employeeNo . ' | Payslip for ' . $payrollDate . '.pdf';

        $pdf = Pdf::loadView('admin.payslip-pdf', [
            'payslip' => $payroll,
            'payslipView' => $payslipView,
            'supervisingOfficer' => $supervisingOfficer,
        ]);

        return response()->streamDownload(fn() => print($pdf->output()), $filename);
    }

    // Get employee by position
    public function getEmployeeByPosition($positionName) {
        $supervisingOfficer = EmployeeInformation::join('employee_personal as ep', 'employee_information.employee_no', '=', 'ep.employee_no')
            ->join('positions as p', 'employee_information.position_id', '=', 'p.id')
            ->where('p.name', $positionName)
            ->selectRaw("p.name as pname, ep.firstname, ep.middlename, ep.lastname, ep.suffix, CONCAT(ep.firstname, ' ', IFNULL(ep.middlename,''), ' ', ep.lastname, ' ', IFNULL(ep.suffix,'')) as full_name")
            ->first();

        return $supervisingOfficer ? [
            'full_name' => $supervisingOfficer->full_name,
            'position_name' => $supervisingOfficer->pname
        ] : ['full_name' => 'N/A', 'position_name' => 'N/A'];
    }

    private function secondHalfFilter($query) {
        return $query->whereRaw("DAY(SUBSTRING_INDEX(cut_off_period, ' to ', 1)) = 16");
    }

    public function downloadDirect($employeeNo, $payrollId)
    {
        $payroll = SalaryItemsPayroll::with(
            'information.section',
            'payroll',
            'deductions.loan.loanType'
        )
        ->where('employee_no', $employeeNo)
        ->where('payroll_id', $payrollId)
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
            'Supervising Administrative Officer'
        );

        $payrollDate = \Carbon\Carbon::parse(
            $payroll->payroll->payroll_date
        )->format('F d, Y');

        $filename = $employeeNo .
            ' | Payslip for ' .
            $payrollDate .
            '.pdf';

        $pdf = Pdf::loadView('admin.payslip-pdf', [
            'payslip' => $payroll,
            'payslipView' => $payslipView,
            'supervisingOfficer' => $supervisingOfficer,
        ]);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }

    public function render()
    {
        $status = $this->status === 'granted' ? 'approved' : $this->status;

        if ($status === 'all') {

            $model = SalaryItemsPayroll::with([
                'information',
                'payroll'
            ]);
        
            if ($this->search) {
                $this->resetPage();
        
                $model->where(function ($query) {
                    $query->where('employee_no', 'like', '%' . $this->search . '%')
                        ->orWhereHas('information.personal', function ($subQuery) {
                            $subQuery->whereRaw(
                                "CONCAT(firstname, ' ', lastname) LIKE ?",
                                ['%' . $this->search . '%']
                            );
                        });
                });
            }
        
        } else {

            $model = EmployeePayslipRequest::with('employee')->where('status', $status)->where('isDeleted', false);

            if ($this->search) {
                $this->resetPage();
                $model->where(function ($query) {
                    $query->where('employee_no', 'like', '%' . $this->search . '%')
                        ->orWhereHas('employee', function ($subQuery) {
                            $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                        });
                });
            }
        }

        $records = $model
    ->join('payroll_salary as ps', 'payroll_salary_items.payroll_id', '=', 'ps.id')
    ->select('payroll_salary_items.*')
    ->orderBy('ps.payroll_date', 'desc')
    ->paginate($this->entries);

        return view('livewire.admin.ess.payslip-request.index', [
            'records' => $records
        ]);
    }
}
