<?php

namespace App\Livewire\Admin\Settings\Hris\Leave;

use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ViewCard extends Component
{

    public $id;
    public $employee_no;
    public $action;
    public $records;

    public $period = [];
    public $particulars = [];
    public $vl_earned = [];
    public $vl_aut_w_pay = [];
    public $vl_bal = [];
    public $vl_aut_wo_pay = [];
    public $sl_earned = [];
    public $sl_aut_w_pay = [];
    public $sl_bal = [];
    public $sl_aut_wo_pay = [];
    public $remarks = [];
    public $vl_total_bal = [];
    public $sl_total_bal = [];
    public $total_bal = [];

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        if (empty($this->id) || empty($this->employee_no) || empty($this->action)) {
            return redirect()->route('leave.show', ['leave' => $this->id]);
        }
    
        $employee = EmployeeInformation::where('employee_no', $this->employee_no)->first();
    
        if (!$employee) {
            return redirect()->route('leave.show', ['leave' => $this->id]);
        }
    
        $records = EmployeeLeaveCard::where('employee_no', $this->employee_no)->get();
    
        $sortedRecords = collect($records)
            ->groupBy('year')
            ->map(function ($items, $year) use ($records) {
                $lastItem = $items->last();
    
                $prevBal = [
                    'vl' => (float)($lastItem['vl_bal'] ?? 0),
                    'sl' => (float)($lastItem['sl_bal'] ?? 0),
                ];
    
                $previousYearRecord = $records->where('year', $year - 1)->last();
    
                if ($previousYearRecord) {
                    $prevBal['vl'] = (float)($previousYearRecord['vl_bal'] ?? 0);
                    $prevBal['sl'] = (float)($previousYearRecord['sl_bal'] ?? 0);
                } else {
                    $prevBal['vl'] = 0;
                    $prevBal['sl'] = 0;
                }
    
                $sortedItems = $items->sortBy(function ($item) {
                    return DateTime::createFromFormat('F', $item['period'])->format('m');
                })->values();
    
                return [
                    'previous_bal' => $prevBal,
                    'items' => $sortedItems
                ];
            })
            ->sortKeys();
    
        $this->records = $sortedRecords;
    
        // Reset arrays
        $this->period = [];
        $this->particulars = [];
        $this->vl_earned = [];
        $this->vl_aut_w_pay = [];
        $this->vl_bal = [];
        $this->vl_aut_wo_pay = [];
        $this->sl_earned = [];
        $this->sl_aut_w_pay = [];
        $this->sl_bal = [];
        $this->sl_aut_wo_pay = [];
        $this->remarks = [];
        $this->vl_total_bal = [];
        $this->sl_total_bal = [];

        foreach ($sortedRecords as $year => $recordData) {
            $this->total_bal[$year] = $recordData['previous_bal'];
            foreach ($recordData['items'] as $record) {
                $this->particulars[$year][] =  $record['particulars'];
                $this->period[$year][] = $record['period'];
                $this->vl_earned[$year][] = $record['vl_earned'] ?? 0;
                $this->vl_aut_w_pay[$year][] = $record['vl_aut_w_pay'] ?? 0;
                $this->vl_bal[$year][] = $record['vl_bal'] ?? 0;
                $this->vl_aut_wo_pay[$year][] = $record['vl_aut_wo_pay'] ?? 0;
                $this->sl_earned[$year][] = $record['sl_earned'] ?? 0;
                $this->sl_aut_w_pay[$year][] = $record['sl_aut_w_pay'] ?? 0;
                $this->sl_bal[$year][] = $record['sl_bal'] ?? 0;
                $this->sl_aut_wo_pay[$year][] = $record['sl_aut_wo_pay'] ?? 0;
                $this->remarks[$year][] = $record['remarks'] ?? '';
            }
        }

    }
    
    public function onChange($code, $year, $key) {
        $previousKey = $key - 1;
        $previousYear = $year - 1;
    
        // Dynamically construct property names
        $balProperty = "{$code}_bal";
        $earnedProperty = "{$code}_earned";
        $autWPayProperty = "{$code}_aut_w_pay";
    
        // Determine the previous balance
        if (isset($this->{$balProperty}[$year][$previousKey])) {
            $previousBal = floatval($this->{$balProperty}[$year][$previousKey]);
        } elseif (isset($this->{$balProperty}[$previousYear])) {
            $previousBal = floatval(end($this->{$balProperty}[$previousYear]));
        } else {
            $previousBal = 0;
        }
    
        // Update the balance for the current year and propagate to future years
        while (isset($this->{$earnedProperty}[$year])) {
            foreach ($this->{$earnedProperty}[$year] as $i => $earned) {
                if ($i < $key) continue; // Skip previous months
    
                $earned = floatval($earned ?? 0); // Ensure earned is treated as a float, default to 0
                $autWPay = isset($this->{$autWPayProperty}[$year][$i]) ? floatval($this->{$autWPayProperty}[$year][$i]) : 0;
    
                // Compute new balance and format to 3 decimal places
                $this->{$balProperty}[$year][$i] = number_format($previousBal + $earned - $autWPay, 3, '.', '');
    
                // Set current as previous for the next iteration
                $previousBal = floatval($this->{$balProperty}[$year][$i]);
            }
    
            // Compute the total balance for the year (fixing the incorrect indexing)
            $this->total_bal[$year + 1][$code] = array_sum(array_map('floatval', $this->{$balProperty}[$year] ?? []));
    
            // Move to the next year
            $year++;
            $key = 0; // Start from the beginning of the next year
        }
    }
    
    public function regroupRecords() {
        $updatedRecords = [];

        foreach ($this->period as $year => $periods) {
            $items = [];

            foreach ($periods as $index => $period) {
                $items[] = [
                    'period' => $period,
                    'particulars' => $this->particulars[$year][$index] ?? '',
                    'vl_earned' => (float)($this->vl_earned[$year][$index] ?? 0),
                    'vl_aut_w_pay' => (float)($this->vl_aut_w_pay[$year][$index] ?? 0),
                    'vl_bal' => (float)($this->vl_bal[$year][$index] ?? 0),
                    'vl_aut_wo_pay' => (float)($this->vl_aut_wo_pay[$year][$index] ?? 0),
                    'sl_earned' => (float)($this->sl_earned[$year][$index] ?? 0),
                    'sl_aut_w_pay' => (float)($this->sl_aut_w_pay[$year][$index] ?? 0),
                    'sl_bal' => (float)($this->sl_bal[$year][$index] ?? 0),
                    'sl_aut_wo_pay' => (float)($this->sl_aut_wo_pay[$year][$index] ?? 0),
                    'remarks' => $this->remarks[$year][$index] ?? '',
                ];
            }

            // Sort by month order
            $items = collect($items)->sortBy(function ($item) {
                return DateTime::createFromFormat('F', $item['period'])->format('m');
            })->values();

            $prevBal = [
                'vl' => (float)($this->total_bal[$year]['vl'] ?? 0),
                'sl' => (float)($this->total_bal[$year]['sl'] ?? 0),
            ];

            $updatedRecords[$year] = [
                'previous_bal' => $prevBal,
                'items' => $items,
            ];
        }

        // Assign the newly constructed structure back to $this->records
        $this->records = $updatedRecords;
    }


    public function save() {

        if (Gate::denies('write leave-credits')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!',
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->regroupRecords();

        try {
            
            DB::transaction(function () {

                if($this->id == 1 || $this->id == 2) {
                    foreach ($this->records as $year => $data) {
                        foreach ($data['items'] as $item) {
                            EmployeeLeaveCard::updateOrCreate(
                                [
                                    'year' => $year,
                                    'period' => $item['period'], 
                                ],
                                [
                                    'particulars' => $item['particulars'],
                                    'vl_earned' => $item['vl_earned'],
                                    'vl_aut_w_pay' => $item['vl_aut_w_pay'],
                                    'vl_bal' => $item['vl_bal'],
                                    'vl_aut_wo_pay' => $item['vl_aut_wo_pay'],
                                    'sl_earned' => $item['sl_earned'],
                                    'sl_aut_w_pay' => $item['sl_aut_w_pay'],
                                    'sl_bal' => $item['sl_bal'],
                                    'sl_aut_wo_pay' => $item['sl_aut_wo_pay'],
                                    'remarks' => $item['remarks'],
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
        return view('livewire.admin.settings.hris.leave.view-card');
    }
}
