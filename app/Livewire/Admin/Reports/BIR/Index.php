<?php

namespace App\Livewire\Admin\Reports\BIR;

use App\Models\EmployeeInformation;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selected_id;
    protected $listeners = ['remove']; 
    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';
    public $year;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function render()
    {
        $model = EmployeeInformation::with('account', 'personal');

       if (!empty($this->search)) {
            $this->resetPage();

            $model->where(function ($q) {
                $q->where('employee_no', 'like', '%' . $this->search . '%')
                ->orWhereHas('personal', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $this->search . '%']);
                });
            });
        }

        $records = $model->paginate($this->entries);

        $records = tap($model->paginate($this->entries))->each(function ($record) {
            $hireDate = \Carbon\Carbon::parse($record->date_hired ?? $record->personal->date_hired ?? null);

            $record->can_generate_2316 = false;

            if ($hireDate) {
                if ($hireDate->year <= (int) $this->year) {
                    $record->can_generate_2316 = true;
                }
            }
        });

        return view('livewire.admin.reports.b-i-r.index', [
            'records' => $records
        ]);

    }

    public function view2316($id)
    {
        return redirect()->route('reports.form-2316', [
            'id' => $id,
            'year' => $this->year,
        ]);
    }
}