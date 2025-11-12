<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2 align-items-center">
            <div class="text-end d-flex justify-content-b align-items-center gap-2">
                <label for="year" class="form-label mb-0">Year:</label>
                <select id="year" wire:model.live="year" class="form-select">
                    @for ($y = now()->year; $y >= 2010; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
       
       <div>
             <div class="text-end d-flex justify-content-b align-items-center gap-2">
                <label for="search" class="form-label mb-0">Search:</label>
                <input id="search" wire:model.live="search" type="text" class="form-control w-100" placeholder="Search something...">
            </div>
        </div>
    </div>
   
    <div class="table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Employee No.</th>
                    <th>Name</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @forelse($records as $record)
                    <tr data-id="{{ $record->employee_no }}">
                        <td class="text-center">
                            <img style="width: 50px; height: 50px;"
                                src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($record->personal->firstname . ' ' . $record->personal->lastname) }}">              
                        </td>
                        <td>{{ $record->employee_no }}</td>
                        <td>
                            {{ trim("{$record->personal->suffix} {$record->personal->firstname} {$record->personal->middlename} {$record->personal->lastname}") }}
                        </td>
                        <td>
                           <button 
                                type="button" 
                                @if (!$record->can_generate_2316) disabled @endif
                                class="btn btn-success mx-1 text-white" 
                                title="BIR 2316" 
                                wire:click="view2316({{ $record->id }})">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center fw-bold py-3">No data was found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>
</div>
