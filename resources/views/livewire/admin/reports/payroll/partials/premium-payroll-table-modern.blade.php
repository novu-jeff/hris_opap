<div class="table-responsive rounded-3 border bg-white">

    <table class="table table-hover align-middle mb-0">

        <thead class="table-light position-sticky top-0">
            <tr>
                <th>ID</th>
                <th>BONUS TYPE</th>
                <th>COVERAGE PERIOD</th>
                <th>PAYROLL DATE</th>
                <th>SEMESTER</th>
                <th>EMPLOYEES</th>
                <th>STATUS</th>
                <th width="140">ACTIONS</th>
            </tr>
        </thead>

        <tbody>

            @foreach($payrolls as $payroll)

                @php

                    $coverage = 'N/A';

                    if ($payroll->coverage_from && $payroll->coverage_to) {

                        $coverage =
                            \Carbon\Carbon::parse($payroll->coverage_from)
                                ->format('M d, Y')
                            . ' - ' .
                            \Carbon\Carbon::parse($payroll->coverage_to)
                                ->format('M d, Y');
                    }

                    $statusClass = match($payroll->status) {
                        'approved' => 'bg-success',
                        'pending' => 'bg-warning text-dark',
                        default => 'bg-danger'
                    };

                @endphp

                <tr class="border-bottom">

                    <!-- ID -->
                    <td class="fw-semibold text-dark">
                        #{{ $payroll->id }}
                    </td>

                    <!-- Employment Type -->

                    <!-- Bonus Type -->
                    <td>
                        <span class="badge bg-info text-dark">
                            {{ strtoupper(str_replace('_', ' ', $payroll->bonus_type)) }}
                        </span>
                    </td>

                    <!-- Coverage -->
                    <td>
                        <span class="badge bg-secondary">
                            {{ strtoupper($coverage) }}
                        </span>
                    </td>

                    <!-- Payroll Date -->
                    <td>
                        {{ \Carbon\Carbon::parse($payroll->payroll_date)->format('M d, Y') }}
                    </td>

                    <!-- Semester -->
                    <td>

                        @if($payroll->semester)

                            <span class="badge bg-dark">

                                {{ strtoupper(str_replace('_', ' ', $payroll->semester)) }}

                            </span>

                        @else

                            <span class="text-muted">
                                N/A
                            </span>

                        @endif

                    </td>

                    <!-- Employees -->
                    <td class="fw-bold">
                        {{ $payroll->employee_count }}
                    </td>

                    <!-- Status -->
                    <td>
                        <span class="badge {{ $statusClass }}">
                            {{ strtoupper($payroll->status) }}
                        </span>
                    </td>

                    <!-- Actions -->
                    <td>

                        <div class="d-flex align-items-center gap-2">

                            <!-- VIEW -->
                            <a
                                target="_blank"
                                href="{{ route('reports.premium.payroll.view', $payroll->id) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="fa fa-eye"></i>
                            </a>

                            <!-- DISAPPROVE -->
                            @if($payroll->status !== 'pending')

                                <button
                                    wire:click="disapprove({{ $payroll->id }})"
                                    class="btn btn-warning btn-sm"
                                    title="Pending Payroll"
                                >
                                    <i class="fa-solid fa-ban"></i>
                                </button>

                            @endif

                            <!-- DELETE -->
                            <button
                                wire:click="remove(true, {{ $payroll->id }})"
                                class="btn btn-danger btn-sm"
                                title="Delete Payroll"
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