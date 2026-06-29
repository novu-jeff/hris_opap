<div class="card border-0 mt-3">
    <div class="card-body p-0">

        <div class="row mb-4">

            <div class="col-md-6 d-flex align-items-center gap-2">
                <label class="form-label mb-0">Show entries:</label>

                <select
                    wire:model.live="entries"
                    class="form-select w-auto">

                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>

                </select>

            </div>

            <div class="col-md-6 d-flex justify-content-end align-items-center gap-2">

                <label class="form-label mb-0">
                    Search:
                </label>

                <input
                    type="text"
                    wire:model.live="search"
                    class="form-control w-50"
                    placeholder="Employee name...">

            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-striped table-bordered">

                <thead>

                    <tr>

                        <th>Employee No.</th>

                        <th>Unit</th>

                        <th>Employee</th>

                        <th>Earned</th>

                        <th>Used</th>

                        <th>Remaining</th>

                        <th>Earned Date</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($records as $record)

                        <tr>

                            <td>
                                {{ $record->employee->employee_no }}
                            </td>
                            <td>
                                <span class="{{ $record->employee->section ? '' : 'text-muted fst-italic' }}"> 
                                    {{ strtoupper(optional($record->employee->section)->code ?? 'NO SECTION') }}
                                </span>  
                            </td>

                            <td>
                                {{ $record->employee->personal->firstname }}
                                {{ $record->employee->personal->lastname }}
                            </td>

                            <td>
                                {{ number_format($record->earned_hours,2) }}
                            </td>

                            <td>
                                {{ number_format($record->used_hours,2) }}
                            </td>

                            <td>

                                <span class="badge bg-success">

                                    {{ number_format($record->remaining_hours,2) }}

                                </span>

                            </td>

                            <td>

                                {{ format_date($record->earned_date,'date_string') }}

                            </td>

                            <td>

                                <a
                                    href="{{ route('ess.offset-credits.edit',$record->id) }}"
                                    class="btn btn-primary btn-sm">

                                    <i class="fa-solid fa-pen"></i>

                                </a>

                                <button
                                    wire:click="remove(true,{{ $record->id }})"
                                    class="btn btn-danger btn-sm">

                                    <i class="fa-solid fa-trash"></i>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center py-4">

                                No Offset Credits Found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-4">

            {{ $records->links(data:['scrollTo'=>false]) }}

        </div>

    </div>

</div>