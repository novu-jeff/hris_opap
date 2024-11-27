<div>
    <div class="modal fade" wire:ignore.self id="add_new_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Add Record</h1>
                    <button type="button" class="btn-close" wire:click="close_upload" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="fields.employee_no">Employee No. <span class="text-danger">*</span></label>
                            <select wire:model="fields.employee_no" wire:change="select_change('employee_no')" id="fields.employee_no" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                @foreach($employees as $employee)
                                    <option value="{{$employee->employee_no}}">#{{$employee->employee_no . ' - ' . $employee->personal->firstname . ' ' . $employee->personal->lastname }}</option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('fields.employee_no') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="fields.amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" wire:model="fields.amount" id="fields.amount" class="form-control">
                            <div class="error-field">
                                @error('fields.amount') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button class="btn btn-primary" wire:click="save">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mb-5 gap-3">
        <a href="{{route('other-deductions.index')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        <button class="btn btn-primary px-5 py-3 text-uppercase" wire:click="addRecords">Add Employee</button>
    </div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0" wire:ignore>
            <table class="table w-100">
                <thead>
                    <tr>
                        <th>Employee No</th>
                        <th>Employee Name</th>
                        <th>Amount</th>
                        <th style="max-width: 200px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    @foreach($records as $record)
                        <tr data-id="{{$record->id}}">
                            <td>#{{$record->employee_no}}</td>
                            <td>{{$record->personal->firstname . ' ' . $record->personal->lastname}}</td>
                            <td>₱{{number_format($record->amount, 2)}}</td>
                            <td>
                                <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>