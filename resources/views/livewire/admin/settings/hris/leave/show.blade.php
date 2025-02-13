<div>
    <div class="mt-4 mb-5">
        <h3 class="text-uppercase fw-bold">For {{$leaveName}}</h3>
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
            <form wire:submit.prevent="save">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Employee No</th>
                                <th>Employee Name</th>
                                @if($this->id == 1 || $this->id == 2)
                                    <th>VL Credits</th>
                                    <th>SL Credits</th>
                                    <th>Updated as of</th>
                                    <th>Actions</th>
                                @else
                                    <th>Credits</th>
                                    <th>Updated as of</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td style="vertical-align: top; padding-top: 22px;">{{ $record->employee_no }}</td>
                                    <td style="vertical-align: top; padding-top: 22px;">{{ $record->personal->firstname . ' ' . $record->personal->lastname }}</td>
                                    @if($this->id == 1 || $this->id == 2)
                                        <td style="vertical-align: top; padding-top: 12px;">
                                            <input 
                                                type="text" 
                                                wire:key="vl-credit-{{$record->employee_no}}" 
                                                wire:model="vl_credits.{{$record->employee_no}}" 
                                                class="form-control {{ $has_leave_card[$record->employee_no] === true ? 'restricted' : '' }}" 
                                                {{ $has_leave_card[$record->employee_no] === true ? 'readonly' : '' }}>
                                            <div class="error-field">
                                                @error("vl_credits.{$record->employee_no}") 
                                                    <span class="text-danger">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                            <label class="mt-2 mb-2">Total: <span style="font-weight: 600; color:red">{{$total_vl_credits[$record->employee_no]}}</span></label>
                                        </td>
                                        <td style="vertical-align: top; padding-top: 12px;">
                                            <input 
                                                type="text" 
                                                wire:key="sl-credit-{{$record->employee_no}}" 
                                                wire:model="sl_credits.{{$record->employee_no}}" 
                                                class="form-control {{ $has_leave_card[$record->employee_no] === true ? 'restricted' : '' }}" 
                                                {{ $has_leave_card[$record->employee_no] === true ? 'readonly' : '' }}>
                                            <div class="error-field">
                                                @error("sl_credits.{$record->employee_no}") 
                                                    <span class="text-danger">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                            <label class="mt-2 mb-2">Total: <span style="font-weight: 600; color:red">{{$total_sl_credits[$record->employee_no]}}</span></label>
                                        </td>
                                        <td style="vertical-align: top; padding-top: 12px;">
                                            <input 
                                                type="month" 
                                                wire:key="as_of-{{$record->employee_no}}" 
                                                wire:model="as_of.{{$record->employee_no}}" 
                                                class="form-control {{ $has_leave_card[$record->employee_no] === true ? 'restricted' : '' }}" 
                                                {{ $has_leave_card[$record->employee_no] === true ? 'readonly' : '' }}>
                                            <div class="error-field">
                                                @error("as_of.{$record->employee_no}") 
                                                    <span class="text-danger">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        </td>      
                                        <td style="vertical-align: top; padding-top: 12px;">
                                            @if($has_leave_card[$record->employee_no])
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <div class="btn btn-danger" wire:click="resetCredit(true, '{{ $record->employee_no }}')">
                                                        <i class="fa-solid fa-rotate"></i>
                                                    </div>
                                                    <a href="{{route('leave.show', ['leave' => $id, 'employee' => $record->employee_no, 'action' => 'view-card'])}}" class="btn btn-primary">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </td>       
                                    @else
                                        <td>
                                            <input 
                                                type="number" 
                                                wire:key="credit-{{$record->employee_no}}" 
                                                wire:model="credits.{{$record->employee_no}}" 
                                                class="form-control {{ $has_leave_card[$record->employee_no] === true ? 'restricted' : '' }}" 
                                                {{ $has_leave_card[$record->employee_no] === true ? 'readonly' : '' }}>
                                            <div class="error-field">
                                                @error("credits.{$record->employee_no}") 
                                                    <span class="text-danger">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        </td>
                                        <td style="vertical-align: top; padding-top: 12px;">
                                            <input 
                                                type="month" 
                                                wire:key="as_of-{{$record->employee_no}}" 
                                                wire:model="as_of.{{$record->employee_no}}" 
                                                class="form-control {{ $has_leave_card[$record->employee_no] === true ? 'restricted' : '' }}" 
                                                {{ $has_leave_card[$record->employee_no] === true ? 'readonly' : '' }}>
                                            <div class="error-field">
                                                @error("as_of.{$record->employee_no}") 
                                                    <span class="text-danger">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        </td>      
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No records found</td>
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
