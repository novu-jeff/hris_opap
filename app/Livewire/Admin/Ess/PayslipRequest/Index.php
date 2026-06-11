<?php

namespace App\Livewire\Admin\Ess\PayslipRequest;

use ZipArchive;
use Illuminate\Support\Facades\File;

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

 

    public function downloadDirect(
        string $employeeNo,
        int $payrollId
    )
    {
        $relativePath =
            "payslips/payroll_{$payrollId}/{$employeeNo}.pdf";
    
        $fullPath =
            storage_path('app/' . $relativePath);
    
        // File already exists
        if (File::exists($fullPath)) {
    
            return response()->download(
                $fullPath,
                "{$employeeNo}.pdf"
            );
    
        }
    
        $service = app(
            \App\Services\PayslipPdfService::class
        );
    
        try {
    
            $generatedPath = $service->generateAndSave(
                $employeeNo,
                $payrollId
            );
    
        } catch (\Throwable $e) {
    
            logger()->error($e);
    
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Unable to generate payslip. Please contact the administrator.',
            ]);
    
        }
    
        if (!$generatedPath) {
    
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Unable to generate payslip.',
            ]);
    
        }
    
        $generatedFullPath =
            storage_path('app/' . $generatedPath);
    
        if (!File::exists($generatedFullPath)) {
    
            return $this->dispatch('alert', [
                'showAlert' => true,
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Payslip file not found.',
            ]);
    
        }
    
        return response()->download(
            $generatedFullPath,
            "{$employeeNo}.pdf"
        );
    }

    public function downloadAllPayslips()
{
    set_time_limit(0);

    $tempDir = storage_path('app/temp');

    if (!File::exists($tempDir)) {
        File::makeDirectory($tempDir, 0755, true);
    }

    $zipName = 'All_SecondHalf_Payslips_' . now()->format('YmdHis') . '.zip';
    $zipPath = $tempDir . '/' . $zipName;

    if (File::exists($zipPath)) {
        File::delete($zipPath);
    }

    $zip = new ZipArchive();

    if (
        $zip->open(
            $zipPath,
            ZipArchive::CREATE | ZipArchive::OVERWRITE
        ) !== true
    ) {
        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Error',
            'message' => 'Unable to create ZIP file.',
        ]);
    }

    $filesAdded = 0;

    SalaryItemsPayroll::query()
        ->join(
            'payroll_salary',
            'payroll_salary_items.payroll_id',
            '=',
            'payroll_salary.id'
        )
        ->whereNotNull('payroll_salary_items.payslip_path')

        // Only second-half payrolls (16th to end of month)
        ->whereRaw("
            DAY(
                STR_TO_DATE(
                    SUBSTRING_INDEX(payroll_salary.cut_off_period, ' to ', 1),
                    '%Y-%m-%d'
                )
            ) = 16

            AND

            STR_TO_DATE(
                SUBSTRING_INDEX(payroll_salary.cut_off_period, ' to ', -1),
                '%Y-%m-%d'
            ) = LAST_DAY(
                STR_TO_DATE(
                    SUBSTRING_INDEX(payroll_salary.cut_off_period, ' to ', -1),
                    '%Y-%m-%d'
                )
            )
        ")
        ->select(
            'payroll_salary_items.*',
            'payroll_salary.cut_off_period'
        )
        ->orderBy('payroll_salary_items.id')
        ->chunk(200, function ($items) use ($zip, &$filesAdded) {

            foreach ($items as $item) {

                $path = storage_path(
                    'app/' . $item->payslip_path
                );

                if (
                    !empty($item->payslip_path) &&
                    File::exists($path)
                ) {

                    $start = explode(
                        ' to ',
                        $item->cut_off_period
                    )[0];

                    $folder = Carbon::parse($start)
                        ->format('F_Y');

                    $zip->addFile(
                        $path,
                        $folder . '/' . basename($path)
                    );

                    $filesAdded++;
                }
            }
        });

    $zip->close();

    // No files found
    if ($filesAdded === 0) {

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'No Payslips Found',
            'message' => 'No second-half payslips found to download.',
        ]);

       
    }

    // Extra safety check
    if (!File::exists($zipPath)) {

        return $this->dispatch('alert', [
            'showAlert' => true,
            'status' => 'error',
            'title' => 'Error',
            'message' => 'ZIP file could not be created.',
        ]);
    }

    return response()
        ->download($zipPath, $zipName)
        ->deleteFileAfterSend(true);
}



    public function test()
{
    dd('WORKING');
}

    public function render()
    {
        $status = $this->status === 'granted'
            ? 'approved'
            : $this->status;
    
        if ($status === 'all') {
    
            $model = SalaryItemsPayroll::with([
                'information.personal',
                'payroll'
            ])
            ->join(
                'payroll_salary as ps',
                'payroll_salary_items.payroll_id',
                '=',
                'ps.id'
            )
            ->select('payroll_salary_items.*')
            ->orderBy('ps.payroll_date', 'desc');
    
            if ($this->search) {
    
                $this->resetPage();
    
                $model->where(function ($query) {
    
                    $query->where(
                        'payroll_salary_items.employee_no',
                        'like',
                        '%' . $this->search . '%'
                    )
    
                    ->orWhereHas('information.personal', function ($subQuery) {
    
                        $subQuery->whereRaw(
                            "CONCAT(firstname, ' ', lastname) LIKE ?",
                            ['%' . $this->search . '%']
                        );
    
                    });
    
                });
    
            }
    
        } else {
    
            $model = EmployeePayslipRequest::with([
                'employee',
                'payroll'
            ])
            ->where('employee_payslip_request.status', $status)
            ->where('employee_payslip_request.isDeleted', false)
            ->latest();
    
            if ($this->search) {
    
                $this->resetPage();
    
                $model->where(function ($query) {
    
                    $query->where(
                        'employee_no',
                        'like',
                        '%' . $this->search . '%'
                    )
    
                    ->orWhereHas('employee', function ($subQuery) {
    
                        $subQuery->whereRaw(
                            "CONCAT(firstname, ' ', lastname) LIKE ?",
                            ['%' . $this->search . '%']
                        );
    
                    });
    
                });
    
            }
    
        }
    
        $records = $model->paginate($this->entries);
    
        return view(
            'livewire.admin.ess.payslip-request.index',
            [
                'records' => $records
            ]
        );
    }
}
