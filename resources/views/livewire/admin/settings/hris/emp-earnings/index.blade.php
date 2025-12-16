<div>
    
    @if($page == 'create' || $page == 'edit')
        <div class="d-flex justify-content-end gap-3 actions w-100 mb-5">
            <!-- <button wire:click="setPage" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Go Back</button> -->
        </div>
        <div class="card border-0 mt-3 shadow">
            <div class="card-body p-4">
                <form wire:submit.prevent="save" wire:target="save">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-3 w-100">
                            <label for="employee_no" class="form-label">Choose Employees</label>
                            <select class="form-select multi-select w-100" wire:model="fields.employee_no"  multiple >
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->employee_no }}">
                                        ({{ $employee->employee_no }}) {{ $employee->personal->firstname }} {{ $employee->personal->lastname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fields.employee_no') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-12 mb-3">
                            <label for="amount_type" class="form-label">Amount Type</label>
                            <input type="text" id="amount_type" wire:model="fields.amount_type" class="form-control restricted" readonly>
                            @error('fields.amount_type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @if(in_array($amountType, ['fixed_amount', 'percentage']))
                            <div class="col-12 col-md-6 mb-3">
                                <label for="first_term" class="form-label">First Term</label>
                                <input type="number" id="first_term" wire:model="fields.first_term" class="form-control">
                                @error('fields.first_term') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="second_term" class="form-label">Second Term</label>
                                <input type="number" id="second_term" wire:model="fields.second_term" class="form-control">
                                @error('fields.second_term') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                            <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                            <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="d-flex justify-content-end gap-3 actions w-100 mb-5">
            <!-- <a href="{{route('other-earnings.index')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a> -->
            <button wire:click="setPage('create')" class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</button>
        </div>
        <div class="card border-0 mt-3">
            <div class="card-body p-0">
                <div class="row mb-4">
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
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Employee No</th>
                                <th>Employee Name</th>
                                <th>First Term</th>
                                <th>Second Term</th>
                                <th>Last Update</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td>{{ $record->employee_no }}</td>
                                    <td>{{ $record->personal->firstname . ' ' . $record->personal->lastname }}</td>
                                    <td>
                                        PHP {{ number_format($record->first_term, 2) }}
                                    </td>
                                    <td>
                                        PHP {{ number_format($record->second_term, 2) }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($record->updated_at)->format('F d, Y \•\ H:i A') }}
                                    </td>   
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button wire:click="setPage('edit', '{{$record->employee_no}}')" class="btn btn-info mx-1">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>
                                            <button wire:click="remove('true', '{{$record->employee_no}}')" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $records->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
    @endif
</div>

@section('script')
<script>
    $(function() {

        Livewire.on('set_select', () => {
            setTimeout(() => {
                $('.multi-select').select2();
                $('.multi-select').on('change', function (e) {
                    const data = $('.multi-select').select2('val');
                    console.log(data);
                    @this.dispatch('onChange', [data ?? null]);
                });

            }, 10);
            
        });

        

    });
</script>
@endsection