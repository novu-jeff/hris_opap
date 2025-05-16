<?php

namespace App\Livewire\Admin\Settings\Hris\Gsis;

use App\Imports\GSISBillingImports;
use App\Models\GSISBilling;
use App\Models\GSISBillingItems;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{

    use WithPagination;
    use WithFileUploads;

    public $isParsing;
    public $isUploading;
    public $resultMessage;
    public $file;
    public $upload_preview;
    public $items;
    public $selected_id;

    protected $listeners = ['remove'];

    protected $paginationTheme = 'bootstrap';
    public $entries = 10;
    public $search = '';


    public function uploadRecords() {
        $this->dispatch('showModal', [
            'modal' => 'uploadBilling'
        ]);
    }

    public function close_upload() {
        $this->reset('upload_preview', 'file');
    }

    public function updatedFile() {
        if ($this->file) {
            $this->resetErrorBag('file');
            $this->upload_preview;
            $file = $this->file;

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $extension = strtolower($file->getClientOriginalExtension());

                if (in_array($extension, ['xls', 'xlsx'])) {
                    try {

                        $files = Storage::files('public/temp/files');
                        Storage::delete($files); 

                        $fileName = uniqid() . '.' . $extension;

                        $file->storeAs('public/temp/files', $fileName);

                        $this->upload_preview = asset('storage/temp/files/' . $fileName);

                        $this->isParsing = false;

                    } catch (\Exception $e) {
                        $this->addError('file', 'There was an error saving the file to temporary storage.');
                        $this->isParsing = false;
                    }
                } else {
                    $this->addError('file', 'The file must be an Excel file (.xls or .xlsx).');
                }
            }

            $this->file = null;

        } else {
            $this->isParsing = true;
        }
    }

    public function upload_file() {

        if (Gate::denies('write gsis-billing')) {
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Access Denied!', 
                'showAlert' => true,
                'message' => 'You do not have permission to perform this action.',
            ]);
            return;
        }

        $this->isUploading = true;
    
        DB::beginTransaction();
    
        try {
            // Correct file path using the Storage facade
            $relativePath = str_replace(asset('storage/'), '', $this->upload_preview);
            $absolutePath = storage_path('app/public/' . $relativePath);
        
            // Check if the file exists in the storage
            if (!Storage::exists('public/' . $relativePath)) {
                throw new \Exception('File does not exist in storage.');
            }
        
            // Load the Excel file to an array
            $sheetsData = Excel::toArray(new GSISBillingImports, $absolutePath);
        
            $sheet = $sheetsData[0];

            $expectedNotNullable = [
                'remitting_agency' => $sheet[0][1],
                'office_code' => $sheet[1][1],
                'billing_month' => $sheet[2][1]
            ];

            $expectedHeaders = [
                'BPNO', 'LastName', 'FirstName', 'MI', 'PREFIX', 'APPELLATION', 'BirthDate', 
                'CRN', 'Basic Monthly Salary', 'Effectivity Date', 'PS', 'GS', 'EC', 
                'CONSOLOAN', 'ECARDPLUS', 'SALARY_LOAN', 'CASH_ADV', 'EMRGYLN', 'EDUC_ASST', 
                'ELA', 'SOS', 'PLREG', 'PLOPT', 'REL', 'LCH_DCS', 'STOCK_PURCHASE', 
                'OPT_LIFE', 'CEAP', 'EDU_CHILD', 'GENESIS', 'GENPLUS', 'GENFLEXI', 
                'GENSPCL', 'HELP', 'GFAL', 'MPL', 'CPL', 'GEL', 'MPL_LITE'
            ];
        
            foreach ($expectedNotNullable as $key => $value) {
                if (empty($value) || is_null($value)) {
                    throw new \Exception("The field '$key' cannot be null or empty.");
                }
            }

            $actualHeaders = array_map('trim', $sheet[4] ?? []); 
        
            if ($actualHeaders !== $expectedHeaders) {
                throw new \Exception('Invalid imported file, format does not match to what\'s expected.');
            }

            $billingMonth = Carbon::createFromFormat('m/Y', $sheet[2][1])->format('m/Y');

            // Find or create the GSIS billing record
            $gsisBilling = GSISBilling::updateOrCreate(
                ['billing_month' => $billingMonth], // condition to check existing record
                [ // data to update or insert
                    'remitting_agency' => $expectedNotNullable['remitting_agency'],
                    'office_code' => $expectedNotNullable['office_code'],
                    'billing_month' => $expectedNotNullable['billing_month'],
                    'date_uploaded' => Carbon::now(),
                ]
            );

            // Determine success message
            $message = 'GSIS Billing for month ' . $billingMonth . ' was ' . ($gsisBilling->wasRecentlyCreated ? 'added' : 'updated') . ' successfully.';

            // Process items starting from the 6th row (index 5) 
            foreach (array_slice($sheet, 5) as $row) {
                $bpNo = $row[0] ?? null;
                $crnNo = $row[7] ?? null;

                // Prepare the data for the billing item
                $data = [
                    'gsis_billing_id' => $gsisBilling->id,
                    'bp_no' => $bpNo,
                    'crn_no' => $crnNo,
                    'effectivity_date' => $row[9] ?? null,
                    'ps' => $row[10] ?? 0,
                    'gs' => $row[11] ?? 0,
                    'ec' => $row[12] ?? 0,
                    'consoloan' => $row[13] ?? 0,
                    'ecardplus' => $row[14] ?? 0,
                    'salary_loan' => $row[15] ?? 0,
                    'cash_adv' => $row[16] ?? 0,
                    'emrgy_loan' => $row[17] ?? 0,
                    'educ_loan' => $row[18] ?? 0,
                    'ela' => $row[19] ?? 0,
                    'sos' => $row[20] ?? 0,
                    'plreg' => $row[21] ?? 0,
                    'plopt' => $row[22] ?? 0,
                    'rel' => $row[23] ?? 0,
                    'lch_dcs' => $row[24] ?? 0,
                    'stock_purchase' => $row[25] ?? 0,
                    'opt_life' => $row[26] ?? 0,
                    'ceap' => $row[27] ?? 0,
                    'edu_child' => $row[28] ?? 0,
                    'genesis' => $row[29] ?? 0,
                    'genplus' => $row[30] ?? 0,
                    'genflexi' => $row[31] ?? 0,
                    'genspcl' => $row[32] ?? 0,
                    'help' => $row[33] ?? 0,
                    'gfal' => $row[34] ?? 0,
                    'mpl' => $row[35] ?? 0,
                    'cpl' => $row[36] ?? 0,
                    'gel' => $row[37] ?? 0,
                    'mpl_lite' => $row[38] ?? 0,
                ];

                // Check if the item exists and update or create
                GSISBillingItems::updateOrCreate(
                    ['gsis_billing_id' => $gsisBilling->id, 'bp_no' => $bpNo, 'crn_no' => $crnNo],
                    $data
                );

            }
                    
            DB::commit();

            $this->dispatch('alert', [
                'status' => 'success',
                'title' => 'Success!',
                'showAlert' => true,
                'message' => $message,
                'redirect' => route('gsis.index')
            ]);
        
        } catch (\Exception $e) {
            DB::rollBack();
        
            logger()->error('Error uploading file: ' . $e->getMessage());
        
            $this->dispatch('alert', [
                'status' => 'error',
                'title' => 'Oops!',
                'isRemoveRowDT' => true,
                'showAlert' => true,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isUploading = false;
        }
        
    }

    public function show(int $id) {

        $records = GSISBilling::with('items')->where('id', $id)->first();

        if(!$records) {
            return redirect()->route('gsis.index');
        }

        $this->items = $records;

    }

    public function remove(bool $isNotify = true, int $id = null) {

        if (Gate::denies('write gsis-billing')) {
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
            $message = 'Please be informed that you are about to delete this GSIS Billing. Once this action is completed, it cannot be undone or reversed!';
            $action = 'remove';

            $this->selected_id = $id;
            $this->dispatch('showConfirmation', [
                'title' => $title,
                'message' => $message,
                'action' => $action
            ]);

        }  else {

            $record = GSISBilling::find($this->selected_id);
                
            if($record) {
                
                $record->delete();

                $this->dispatch('alert', [
                    'status' => 'success',
                    'title' => 'Success!',
                    'id' => $this->selected_id,
                    'isRemoveRowDT' => true,
                    'message' => 'GSIS Billing for ' . strtoupper($record->billing_month) . ' deleted successfully.' 
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

    public function render()
    {


        $model = GSISBilling::with('items');

        if ($this->search) {

            $this->resetPage(); 

            $records = $model->where('remitting_agency', 'like', '%' . $this->search . '%')
                ->orWhere('office_code', 'like', '%' . $this->search . '%')
                ->orWhere('billing_month', 'like', '%' . $this->search . '%');
        }

        $records = $model->latest()->paginate($this->entries);

        return view('livewire.admin.settings.hris.gsis.index', [
            'records' => $records
        ]);
    }
}
