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
    public $sl_credits = [];
    public $vl_credits = [];
    public $as_of = [];
    public $total_sl_credits = [];
    public $total_vl_credits = [];
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

        $currentMonth = strtoupper(Carbon::now()->format('F'));
        $currentYear = Carbon::now()->year;
        $currentMonthYear = Carbon::now()->format('Y-m');

        
        $this->vl_credits = [];
        $this->sl_credits = [];
        $this->total_vl_credits = [];
        $this->total_sl_credits = [];
        $this->as_of = [];
    
        if($this->id == 1 || $this->id == 2) {
            foreach ($employees as $employee) {

                $currentleaveCredits = EmployeeLeaveCard::where('employee_no', $employee['employee_no'])
                    ->where('year', $currentYear)
                    ->orderBy('year', 'asc') 
                    ->get();

                $currentMonthCredits = $currentleaveCredits->filter(function ($item) use ($currentMonth) {
                    return $item->period === $currentMonth; // Compare period with the current month
                })->first();

                $leaveTotalCredits = $currentleaveCredits->last();
                    
                $leaveTotalCreditsVL = $leaveTotalCredits ? $leaveTotalCredits->vl_bal ?? 0 : 0;
                $leaveTotalCreditsSL = $leaveTotalCredits ? $leaveTotalCredits->sl_bal ?? 0 : 0;

                $this->vl_credits[$employee['employee_no']] = $currentMonthCredits ? $currentMonthCredits->vl_bal : 0;
                $this->sl_credits[$employee['employee_no']] = $currentMonthCredits ? $currentMonthCredits->sl_bal : 0;
                $this->as_of[$employee['employee_no']] = ($leaveTotalCreditsVL <= 0 || $leaveTotalCreditsSL <= 0) ? '' : $currentMonthYear;
                $this->total_vl_credits[$employee['employee_no']] = $leaveTotalCreditsVL  ?? null;
                $this->total_sl_credits[$employee['employee_no']] = $leaveTotalCreditsSL  ?? null;

                $this->has_leave_card[$employee['employee_no']] = ($leaveTotalCreditsVL <= 0 || $leaveTotalCreditsSL <= 0) ? false : true;
            }
        } else {
            $leaveCredits = LeaveCredits::where('leave_type_id', $this->id)->get();
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
            'vl_credits.*' => 'required|numeric|gt:0',
            'sl_credits.*' => 'required|numeric|gt:0',
            'as_of.*' => 'required'
        ];
    
        return $rules;
    }
    
    

    protected function messages() {
        return [
            'vl_credits.*.required' => '*required',
            'vl_credits.*.min' => '*required',
            'vl_credits.*.numeric' => '*number only',
            'vl_credits.*.gt' => '*required',

            'sl_credits.*.required' => '*required',
            'sl_credits.*.min' => '*required',
            'sl_credits.*.numeric' => '*number only',
            'sl_credits.*.gt' => '*required',

            'as_of.*' => '*required'
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

        } else {
               
        
            // Retrieve the EmployeeLeaveCard records
            $leaveCard = EmployeeLeaveCard::where('employee_no', $this->selected_id);

            $leaveCardData = $leaveCard->get();
            // Ensure records exist before performing any actions
            if ($leaveCardData->isNotEmpty()) {
                // Reset leave card values
                $leaveCardData->each(function ($card) {
                    $card->{'particulars'} = '';
                    $card->{'vl_earned'} = '';
                    $card->{'vl_aut_w_pay'} = '';
                    $card->{'vl_aut_wo_pay'} = '';
                    $card->{'vl_bal'} = '';

                    $card->{'sl_earned'} = '';
                    $card->{'sl_aut_w_pay'} = '';
                    $card->{'sl_aut_wo_pay'} = '';
                    $card->{'sl_bal'} = '';
                    $card->{'remarks'} = '';
                    $card->save();
                });
        

                foreach ([1, 2] as $leave_id) {
                    $record = LeaveCredits::where('employee_no', $this->selected_id)
                        ->where('leave_type_id', $leave_id) 
                        ->first();
                
                    if ($record) {
                        $record->update([
                            'credits' => 0,
                            'as_of' => '', 
                        ]);
                    }
                }
                
        
                // Check if the last leave card has empty 'vl_bal' and 'sl_bal' fields
                $lastLeaveCard = $leaveCardData->last();
        
                // Only delete if the last card has empty values for 'vl_bal' and 'sl_bal'
                if (empty($lastLeaveCard->vl_bal) && empty($lastLeaveCard->sl_bal)) {
                    // Deleting only the last leave card if conditions are met
                    $leaveCard->delete();
                }
        
                // Reload records
                $this->loadRecords();
        
                // Dispatch success alert
                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'message' => 'Leave Card for ' . strtoupper($record->employee_no) . ' has been reset successfully'
                ]);
            } else {
                // If records don't exist, dispatch error alert
                $this->dispatch('alert', [
                    'showAlert' => true,
                    'status' => 'error',
                    'title' => 'Oops!',
                    'message' => 'Error: ID does not exist'
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

                if($this->id == 1 || $this->id == 2) {
                    foreach ($this->vl_credits as $employeeNo => $credit) {
                        if (!is_null($credit)) {
    
                            LeaveCredits::updateOrCreate(
                                [
                                    'employee_no' => $employeeNo,
                                    'leave_type_id' => 1,
                                ],
                                [
                                    'credits' => $credit,
                                    'as_of' => $this->as_of[$employeeNo] ?? null,
                                ]
                            );
    
                            $leaveCardExists = EmployeeLeaveCard::where('employee_no', $employeeNo)
                                ->where('year', Carbon::now()->year)
                                ->whereNotNull("vl_bal")
                                ->exists();
    
    
                            if(!$leaveCardExists) {
                                $LeaveCardService = new LeaveCardService;
                                $LeaveCardService->init($employeeNo, 'firstime', [
                                    'employee_no' => $employeeNo,
                                    'leave_id' => 1,
                                    'credits' => $credit,
                                    'as_of' => $this->as_of[$employeeNo] ?? null
                                ]);
    
                            }
                        }
                    }
    
                    foreach ($this->sl_credits as $employeeNo => $credit) {
                        if (!is_null($credit)) {
    
                            LeaveCredits::updateOrCreate(
                                [
                                    'employee_no' => $employeeNo,
                                    'leave_type_id' => 2,
                                ],
                                [
                                    'credits' => $credit,
                                    'as_of' => $this->as_of[$employeeNo] ?? null,
                                ]
                            );
    
                            $leaveCardExists = EmployeeLeaveCard::where('employee_no', $employeeNo)
                                ->where('year', Carbon::now()->year)
                                ->whereNotNull("sl_bal")
                                ->exists();
    
    
                            if(!$leaveCardExists) {
                                $LeaveCardService = new LeaveCardService;
                                $LeaveCardService->init($employeeNo, 'firstime', [
                                    'employee_no' => $employeeNo,
                                    'leave_id' => 2,
                                    'credits' => $credit,
                                    'as_of' => $this->as_of[$employeeNo] ?? null
                                ]);
    
                            }
                        }
                    }
                } else {

                    foreach($this->credits as $employeeNo => $credit) {
                        if (!is_null($credit)) {

                            LeaveCredits::updateOrCreate(
                                [
                                    'employee_no' => $employeeNo,
                                    'leave_type_id' => $this->id,
                                ],
                                [
                                    'credits' => $credit,
                                    'as_of' => $this->as_of[$employeeNo],
                                ]
                            );

                        }
                    }

                }

                $this->loadRecords();
        
              
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
