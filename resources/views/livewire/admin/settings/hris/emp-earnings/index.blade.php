<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="row mb-4">
                <div class="col-12 mb-5">
                    <div class="card shadow">
                        <div class="card-body p-4">
                            <form wire:submit.prevent="upload_file">
                                <label for="label" class="mb-1">File Upload</label>
                                <input type="file" wire:model="file" id="upload_file" class="form-control">
                                <div class="d-flex justify-content-end">
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                                            <span wire:loading.remove wire:target="upload_file">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                                            <span wire:loading wire:target="upload_file">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label for="entries" class="form-label mb-0">Show entries:</label>
                    <select id="entries" wire:model.live="entries" class="form-select w-auto">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                    <label for="search" class="form-label mb-0">Search:</label>
                    <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
                </div>
            </div>
            <form wire:submit.prevent="save">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Employee No</th>
                                <th>Employee Name</th>
                                <th>Amount</th>
                                <th>As of</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td>{{ $record->employee_no }}</td>
                                    <td>{{ $record->personal->firstname . ' ' . $record->personal->lastname }}</td>
                                    <td>
                                        <input type="text" wire:key="deduction-{{$record->employee_no}}" wire:model="earnings.{{$record->employee_no}}.amount" class="form-control">
                                        <div class="error-field mt-2">
                                            @error('earnings.' . $record->employee_no . '.amount') 
                                                <span class="text-danger">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <input type="date" wire:key="as_of-{{$record->employee_no}}" wire:model="earnings.{{$record->employee_no}}.as_of" class="form-control">
                                        <div class="error-field mt-2">
                                            @error('earnings.' . $record->employee_no . '.as_of') 
                                                <span class="text-danger">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                    </td>   
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($records->total() > 0)
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                            <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                            <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                        </button>
                    </div>
                @endif
                <div class="mt-4">
                    {{ $records->links(data: ['scrollTo' => false]) }}
                </div>
            </form>
        </div>
    </div>
</div>
