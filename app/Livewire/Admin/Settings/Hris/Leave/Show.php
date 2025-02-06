<?php

namespace App\Livewire\Admin\Settings\Hris\Leave;

use App\Http\Controllers\Admin\Services\LeaveCardService;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public $selected_id;
    public $id;
    public $entries = 10;
    public $search = '';
    public $credits = [];
    public $as_of = [];
    public $total_credits = [];
    public $has_leave_card = [];

    public $leaveName;
    protected $listeners = ['resetCredit'];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $employees = EmployeeInformation::with(['personal'])
            ->where('employment_type_id', 1)
            ->get();
    
        $leaveType = LeaveType::where('id', $this->id)
            ->first();
        $leaveTypes = strtolower($leaveType->code);

        $this->leaveName = $leaveType->name;

        $leaveCredits = LeaveCredits::where('leave_type_id', $this->id)->get();
        
        $this->credits = [];
        $this->as_of = [];
    
        if($this->id == 1 || $this->id == 2) {
            foreach ($employees as $employee) {
                $leaveCredit = $leaveCredits->firstWhere('employee_no', $employee['employee_no']);
                $leaveCardExists = EmployeeLeaveCard::where('employee_no', $employee['employee_no'])
                    ->where('year', Carbon::now()->year)
                    ->whereNotNull("{$leaveTypes}_bal")
                    ->exists();

                $leaveTotalCredits = EmployeeLeaveCard::where('employee_no', $employee['employee_no'])
                    ->where('year', Carbon::now()->year)
                    ->orderBy('year', 'asc') 
                    ->get()
                    ->last();

                $leaveTotalCredits = $leaveTotalCredits ? $leaveTotalCredits->{$leaveTypes . '_bal'} ?? 0 : 0;
    
                $this->credits[$employee['employee_no']] = $leaveCredit ? $leaveCredit->credits : 0;
                $this->as_of[$employee['employee_no']] = $leaveCredit ? $leaveCredit->as_of : null;
                $this->total_credits[$employee['employee_no']] = $leaveTotalCredits  ?? null;
                $this->has_leave_card[$employee['employee_no']] = $leaveCardExists;
            }
        } else {
            foreach ($employees as $employee) {
                $leaveCredit = $leaveCredits->firstWhere('employee_no', $employee['employee_no']);

                $this->credits[$employee['employee_no']] = $leaveCredit ? $leaveCredit->credits : 0;
                $this->as_of[$employee['employee_no']] = $leaveCredit ? $leaveCredit->as_of : null;
                $this->has_leave_card[$employee['employee_no']] = false;
            }
        }

    }
    
    protected function rules() {
        $rules = [
            'credits.*' => 'required|numeric|min:1', 
        ];
    
        // Apply custom validation for as_of when the id is 1 or 2
        if ($this->id == 1 || $this->id == 2) {
            // Apply custom rule for 'as_of' field
            $rules['as_of.*'] = [
                function ($attribute, $value, $fail) {
                    // Get employee number from the attribute key, assuming attribute is like 'as_of.1', 'as_of.2', etc.
                    $employeeNo = explode('.', $attribute)[1] ?? null;
    
                    // Ensure 'as_of' is required when credits are greater than zero
                    if (isset($this->credits[$employeeNo]) && $this->credits[$employeeNo] > 0 && empty($value)) {
                        $fail('The "As of" date is required when credits are greater than zero.');
                    }
                }
            ];
        }
    
        return $rules;
    }
    

    protected function messages() {
        return [
            'credits.*.required' => 'The credits field is required',
            'credits.*.min' => 'The credits field is required',
            'credits.*.numeric' => 'The credits field ',
        ];
    }

    public function resetCredit(bool $isNotify = true, string $employee_no = null) {

        if (Gate::denies('write leave-credits')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }
        
        if($isNotify) {

            $title = 'Are you sure to continue?';
            $message = 'Please be informed that you are about to reset this employee\'s leave card. Once this action is processed, it cannot be undone or reversed!';
            $action = 'resetCredit';

            $this->selected_id = $employee_no;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = LeaveCredits::where('employee_no', $this->selected_id)
                ->where('leave_type_id', $this->id)
                ->first();

            $leaveCard = EmployeeLeaveCard::where('employee_no', $this->selected_id);
                
            if($record && $leaveCard) {
                
                $record->credits = 0;
                $record->as_of = '';

                $record->save();
                $leaveCard->delete();

                $this->loadRecords();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!', 
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => false,
                    'message' => 'Leave Card for ' . strtoupper($record->employee_no) . ' has been reset successfully' 
                ]);

                
            } else {
                return $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!', 
                    'isRemoveRowDT' => false,
                    'message' => 'Error: ID does not exists' 
                ]);
            }
        }

    }
    
    public function save()
    {
        if (Gate::denies('write leave-credits')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }
    
        $this->validate();
    
        try {
            DB::transaction(function () {
                foreach ($this->credits as $employeeNo => $credit) {
                    if (!is_null($credit)) {

                        LeaveCredits::updateOrCreate(
                            [
                                'employee_no' => $employeeNo,
                                'leave_type_id' => $this->id,
                            ],
                            [
                                'credits' => $credit,
                                'as_of' => $this->as_of[$employeeNo] ?? null,
                            ]
                        );

                        $year = Carbon::now()->format('Y');

                        $leaveCard = EmployeeLeaveCard::where('employee_no', $employeeNo)
                            ->where('year', $year)
                            ->count();

                        if($leaveCard == 0) {
                            $LeaveCardService = new LeaveCardService;
                            $LeaveCardService->init($employeeNo, 'firstime', [
                                'employee_no' => $employeeNo,
                                'leave_id' => $this->id,
                                'credits' => $credit,
                                'as_of' => $this->as_of[$employeeNo] ?? null
                            ]);

                            $this->loadRecords();
                        }
                    }
                }
            });
    
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Saved!',
                'showAlert' => true,
                'message' => 'Leave credits updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Error!',
                'showAlert' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    public function render()
    {
        $model = EmployeeInformation::with(['personal'])
            ->where('employment_type_id', 1);
    
        if ($this->search) {
            $this->resetPage(); 
            $model->where(function ($query) {
                $query->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        }
    
        $records = $model->paginate($this->entries);
    
        return view('livewire.admin.settings.hris.leave.show', [
            'records' => $records,
        ]);
    }
}
