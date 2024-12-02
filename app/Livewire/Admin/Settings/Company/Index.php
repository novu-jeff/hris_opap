<?php

namespace App\Livewire\Admin\Settings\Company;

use App\Models\CompanyBusinessType;
use App\Models\CompanyInformation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{

    public array $fields;
    public $records;
    public $types;

    public function mount() {
        $this->loadRecords();
    }

    public function loadRecords() {
        $types = CompanyBusinessType::all();
        $this->types = $types;

        $records = CompanyInformation::first();

        return $this->fields = [
            'name' => $records->name ?? null,
            'address' => $records->address ?? null,
            'contact' => $records->contact ?? null,
            'type' => $records->type_id ?? null,
        ];

    }

    protected function rules() {
        return [
            'fields.type' => 'exists:company_business_types,id',
        ];
    }
    
    public function save() {
    
        $this->validate();
    
        DB::beginTransaction(); // Start the transaction
    
        try {
            // Check if a record with 'id' = 1 exists
            $company = CompanyInformation::where('id', 1)->first();
    
            if ($company) {
                // If a record exists, update it
                $company->update([
                    'name' => $this->fields['name'] ?? null,
                    'address' => $this->fields['address'] ?? null,
                    'contact' => $this->fields['contact'] ?? null,
                    'type_id' => $this->fields['type'] ?? null,
                ]);
            } else {
                // If no record exists, create a new one
                CompanyInformation::create([
                    'name' => $this->fields['name'] ?? null,
                    'address' => $this->fields['address'] ?? null,
                    'contact' => $this->fields['contact'] ?? null,
                    'type_id' => $this->fields['type'] ?? null,
                ]);
            }
    
            DB::commit(); // Commit the transaction
    
            // Dispatch success alert
            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!', 
                'showAlert' => true,
                'message' => 'Company information was updated.'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
    
            // Dispatch error alert
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!', 
                'showAlert' => true,
                'message' => 'Error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.company.index');
    }
}
