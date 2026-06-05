<div class="table-responsive rounded-3 border bg-white">

    <table class="table table-hover align-middle mb-0">

        <thead class="table-light position-sticky top-0">
            <tr>
                <th>ID</th>
                <th>EMPLOYMENT TYPE</th>
                <th>PAY PERIOD</th>
                <th>EMPLOYEES</th>
                <th>TOTAL OT PAY</th>
                <th>STATUS</th>
                <th width="140">ACTIONS</th>
            </tr>
        </thead>

        <tbody>

            @foreach($payrolls as $payroll)

                @php

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
                    <td>
                        <span class="badge bg-info text-dark">
                            {{ strtoupper($payroll->employment_type_name ?? 'N/A') }}
                        </span>
                    </td>

                    <!-- Period -->
                    <td>
                        <span class="badge bg-secondary">
                            {{ strtoupper($payroll->period) }}
                        </span>
                    </td>

                    <!-- Employees -->
                    <td class="fw-bold">
                        {{ $payroll->employee_count }}
                    </td>

                    <!-- Total OT Pay -->
                    <td class="fw-bold text-success">
                        ₱{{ number_format($payroll->items_sum_net_amount ?? 0, 2) }}
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
                                href="{{ route('reports.ot.payroll.view', $payroll->id) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="fa fa-eye"></i>
                            </a>

                            <!-- DISAPPROVE -->
                            @if($payroll->status !== 'pending')

                                <button
                                    wire:click="disapprove({{ $payroll->id }})"
                                    class="btn btn-warning btn-sm"
                                    title="Move Back To Pending"
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