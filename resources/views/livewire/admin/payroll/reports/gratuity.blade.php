<div>

    <!-- FILTERS -->
    <div class="row mb-3 mt-5">

        <!-- Show Entries -->
        <div class="col-md-6 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0 fw-semibold">
                Show entries:
            </label>

            <select
                id="entries"
                wire:model.change="entries"
                class="form-select w-auto"
            >
                @foreach([5,10,20,30,40,50,60,70,80,90,100] as $num)
                    <option value="{{ $num }}">
                        {{ $num }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status -->
        <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
            <label for="status" class="form-label mb-0 fw-semibold">
                Filter Status:
            </label>

            <select
                wire:model.change="status"
                id="status"
                class="form-select w-50"
            >
                <option value=""> - ALL - </option>
                <option value="pending"> Pending </option>
                <option value="approved"> Approved </option>
                <option value="disapproved"> Disapproved </option>
            </select>
        </div>

    </div>


    <!-- ACCORDION -->
    <div class="accordion" id="midYearAccordion">

        @forelse($groupedSalary as $month => $records)

            @php
                $monthKey = \Illuminate\Support\Str::slug($month);

                $approvedCount = $records->where('status', 'approved')->count();
                $pendingCount = $records->where('status', 'pending')->count();
                $disapprovedCount = $records->where('status', 'disapproved')->count();
            @endphp

            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">

                <!-- HEADER -->
                <h2 class="accordion-header">

                    <button
                        class="accordion-button collapsed fw-bold"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $monthKey }}"
                        aria-expanded="false"
                    >

                        <div class="d-flex justify-content-between align-items-center w-100 pe-3">

                            <!-- Month -->
                            <span class="fs-5">
                                {{ $month }}
                            </span>

                            <!-- Status Badges -->
                            <div class="d-flex gap-2">

                                <span class="badge bg-success">
                                    {{ $approvedCount }} Approved
                                </span>

                                <span class="badge bg-warning text-dark">
                                    {{ $pendingCount }} Pending
                                </span>

                                @if($disapprovedCount > 0)
                                    <span class="badge bg-danger">
                                        {{ $disapprovedCount }} Disapproved
                                    </span>
                                @endif

                            </div>

                        </div>

                    </button>

                </h2>


                <!-- BODY -->
                <div
                    id="{{ $monthKey }}"
                    class="accordion-collapse collapse"
                    data-bs-parent="#midYearAccordion"
                >

                    <div class="accordion-body bg-light">

                        <div class="table-responsive bg-white rounded-3 border">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Payroll Date</th>
                                        <th>Status</th>
                                        <th width="180">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($records as $record)

                                        @php
                                            $formattedDate = \Carbon\Carbon::parse(
                                                $record->payroll_date
                                            )->format('F d, Y');

                                            $coverage = '';

                                            if ($record->coverage_from && $record->coverage_to) {
                                                $coverage =
                                                    \Carbon\Carbon::parse($record->coverage_from)->format('M d, Y')
                                                    . ' - ' .
                                                    \Carbon\Carbon::parse($record->coverage_to)->format('M d, Y');
                                            }

                                            $isApproved = $record->status === 'approved';
                                        @endphp

                                        <tr>

                                            <!-- ID -->
                                            <td class="fw-bold">
                                                #{{ format_id($record->id, 6) }}
                                            </td>

                                            <!-- Payroll Date -->
                                            <td>
                                                {{ $formattedDate }}
                                            </td>

                                            <!-- Status -->
                                            <td>
                                                <span class="badge
                                                    {{
                                                        $record->status === 'approved'
                                                            ? 'bg-success'
                                                            : ($record->status === 'pending'
                                                                ? 'bg-warning text-dark'
                                                                : 'bg-danger')
                                                    }}
                                                ">
                                                    {{ strtoupper($record->status) }}
                                                </span>
                                            </td>

                                            <!-- ACTIONS -->
                                            <td>

                                                <div class="d-flex align-items-center gap-2">

                                                    <!-- View -->
                                                    <a
                                                        href="{{ route('payroll.process', [
                                                            'type' => $type,
                                                            'payroll_id' => $record->id
                                                        ]) }}"
                                                        class="btn btn-sm btn-primary"
                                                    >
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    <!-- Regenerate -->
                                                    @if(!$isApproved)
                                                        <button
                                                            class="btn btn-sm btn-info"
                                                            title="Regenerate Payroll"
                                                            wire:click="regeneratePayroll('{{ $record->id }}')"
                                                        >
                                                            <i class="fa-solid fa-arrows-rotate"></i>
                                                        </button>
                                                    @endif

                                                    <!-- Delete -->
                                                    <button
                                                        class="btn btn-sm btn-danger"
                                                        wire:click="removePayroll(true, '{{ $record->id }}')"
                                                    >
                                                        <i class="fa fa-trash"></i>
                                                    </button>

                                                </div>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="text-center py-5 fw-bold">
                No data was found
            </div>

        @endforelse

    </div>


    <!-- PAGINATION -->
    <div class="mt-4">
        {{ $salary->links(data: ['scrollTo' => false]) }}
    </div>

</div>