<?php

namespace App\Livewire\Admin\Ess\ProfileApproval;

use App\Models\EmployeePersonal;
use App\Models\EmployeeUpdateChildren;
use App\Models\EmployeeUpdateCivilService;
use App\Models\EmployeeUpdateEducation;
use App\Models\EmployeeUpdateEmploymentHistory;
use App\Models\EmployeeUpdateOtherWorks;
use App\Models\EmployeeUpdateParents;
use App\Models\EmployeeUpdatePersonal;
use App\Models\EmployeeUpdateSkillsHobbies;
use App\Models\EmployeeUpdateTrainings;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $selected_id;
    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';
    protected $listeners = ['remove'];


    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $models = [
            'personal' => EmployeeUpdatePersonal::class,
            'family' => EmployeeUpdateParents::class,
            'children' => EmployeeUpdateChildren::class,
            'education' => EmployeeUpdateEducation::class,
            'employment-history' => EmployeeUpdateEmploymentHistory::class,
            'civil-service' => EmployeeUpdateCivilService::class,
            'trainings' => EmployeeUpdateTrainings::class,
            'other-works' => EmployeeUpdateOtherWorks::class,
            'skills' => EmployeeUpdateSkillsHobbies::class,
        ];

        $data = collect();

        foreach ($models as $type => $model) {
            $records = $model::select('employee_no', 'updated_at')->get();

            foreach ($records as $record) {
                $data->push([
                    'type' => strtolower($type),
                    'employee_no' => $record->employee_no,
                    'updated_at' => $record->updated_at,
                ]);
            }
        }

        $grouped = $data->groupBy('employee_no')->map(function ($items, $employee_no) {
// dd( $items);
    $latest = $items->sortByDesc('updated_at')->first();
    $types = $items->pluck('type')->unique();

    $personal = EmployeePersonal::where('employee_no', $employee_no)->first();

    return [
        'employee_no' => $employee_no,
        'type' => $latest['type'],
        'types' => str_replace('-', ' ', $types->implode(', ')),
        'name' => $personal
            ? trim("{$personal->firstname} {$personal->middlename} {$personal->lastname}")
            : 'N/A',

        // IMPORTANT: store raw timestamp for sorting
        'date_applied_raw' => $latest['updated_at'] ?? null,

        // Display version only
        'date_applied' => $latest['updated_at']
            ? \Carbon\Carbon::parse($latest['updated_at'])->format('M d, Y')
            : 'N/A',
    ];

});

        if ($this->search) {
            $search = strtolower($this->search);
            $grouped = $grouped->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['employee_no']), $search) ||
                    str_contains(strtolower($item['name']), $search);
            })->values();
        }

       $grouped = $grouped->sortByDesc('date_applied_raw')->values();

    //    dd($grouped);

       $grouped = $grouped->values()->map(function ($item, $i) {
            $item['id'] = $i + 1;
            return $item;
        });

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = $this->entries;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($currentPage, $perPage)->values(),
            $grouped->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.admin.ess.profile-approval.index', [
            'records' => $paginated,
        ]);
    }



}
