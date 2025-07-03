<div>
    <div class="row mb-3 mt-5">
        <div class="col-md-6 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.change="entries" class="form-select w-auto">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
            <label for="search" class="form-label mb-0">Filter Status:</label>
            <select wire:model.change="status" id="status" class="form-select w-50">
                <option value=""> - ALL - </option>
                <option value="pending"> Pending </option>
                <option value="approved"> Approved </option>
                <option value="disapproved"> Disapproved </option>
            </select>
        </div>
    </div>
    <div class="table-respo">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                @forelse($salary as $record)
                    <tr data-id="{{$record->id}}">
                        <td>#{{format_id($record->id, 6)}}</td>
                        <td>
                            @php
                                $dates = explode(' to ', $record->period);
                                $startDate = \Carbon\Carbon::parse($dates[0])->format('F d, Y');
                                $endDate = \Carbon\Carbon::parse($dates[1])->format('F d, Y');
                            @endphp
                            {{ $startDate }} - {{ $endDate }}
                        </td>
                        <td>{{$record->status}}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{route('payroll.process', ['type' => $type, 'payroll_id' => $record->id])}}" class="btn btn-primary">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <button class="btn btn-info" title="Regenerate Payroll" wire:click="regeneratePayroll('{{$record->id}}')">
                                    <i class="fa-solid fa-arrows-rotate fa-spin"></i>
                                </button>
                                <button class="btn btn-danger" wire:click="removePayroll('true', '{{$record->id}}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                    </tr> 
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $salary->links(data: ['scrollTo' => false]) }}
    </div>
</div>
