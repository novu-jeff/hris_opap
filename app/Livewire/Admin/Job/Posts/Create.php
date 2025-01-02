<?php

namespace App\Livewire\Admin\Job\Posts;

use App\Models\EmployementTypes;
use App\Models\JobPosts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Create extends Component
{

    public $position;
    public $company_name;
    public $location;
    public $setup;
    public $type;
    public $slot;
    public $min_salary;
    public $max_salary;
    public $description;
    public $employment_types;

    protected $listeners = ['ckeditor'];

    public function mount() {
        $this->employment_types = EmployementTypes::all();
    }

    public function ckeditor($data) {
        $this->description = $data;
    }

    public function rules() {
        return [
            'position' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'setup' => 'required|string|in:work from home,onsite,hybrid',
            'type' => 'required|exists:employment_types,id',
            'slot' => 'required|integer|min:1|max:100',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0|gt:min_salary',
            'description' => 'required|string',
        ];
    }

    public function messages() {
        return [
            'position.required' => 'The position field is required.',
            'position.string' => 'The position must be a valid string.',
            'position.max' => 'The position may not be greater than 255 characters.',

            'company_name.required' => 'The company name field is required.',
            'company_name.string' => 'The company name must be a valid string.',
            'company_name.max' => 'The company name may not be greater than 255 characters.',

            'location.required' => 'The location field is required.',
            'location.string' => 'The location must be a valid string.',
            'location.max' => 'The location may not be greater than 255 characters.',

            'setup.required' => 'The setup field is required.',
            'setup.string' => 'The setup must be a valid string.',
            'setup.in' => 'The setup must be one of the following: work from home, onsite, or hybrid.',

            'type.required' => 'The job type field is required.',
            'type.string' => 'The job type must be a valid string.',
            'type.in' => 'The job type must be one of the following: regular, contractual, part time, freelance, or project base.',

            'slot.required' => 'The slot field is required.',
            'slot.integer' => 'The slot must be an integer.',
            'slot.min' => 'The slot must be at least 1.',
            'slot.max' => 'The maximum slot is 100.',

            'min_salary.required' => 'The minimum salary field is required.',
            'min_salary.numeric' => 'The minimum salary must be a number.',
            'min_salary.min' => 'The minimum salary must be at least 0.',

            'max_salary.required' => 'The maximum salary field is required.',
            'max_salary.numeric' => 'The maximum salary must be a number.',
            'max_salary.min' => 'The maximum salary must be at least 0.',
            'max_salary.gt' => 'The maximum salary must be greater than the minimum salary.',

            'description.required' => 'The job description field is required.',
            'description.string' => 'The job description must be a valid string.',
        ];
    }

    public function save() {
    
        if (Gate::denies('write jobs')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->validate();
        
        DB::beginTransaction();

        try {

            JobPosts::create([
                'position' => $this->position,
                'company_name' => $this->company_name,
                'location' => $this->location,
                'setup' => $this->setup,
                'employment_type_id' => $this->type,
                'min_salary' => $this->min_salary,
                'max_salary' => $this->max_salary,
                'description' => $this->description,
                'slots' => $this->slot,
            ]);

            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'resetFields' => true,
                'message' => 'Job as ' . strtoupper($this->position) . ' at ' . strtoupper($this->company_name) . ' has been posted!',
            ]);
            
        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'message' => 'Error occured: ' . $e->getMessage()
            ]);
        }

    }

    public function render()
    {
        return view('livewire.admin.job.posts.create');
    }
}
