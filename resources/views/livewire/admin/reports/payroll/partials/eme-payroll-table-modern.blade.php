<div class="table-responsive rounded-3 border bg-white">
    <table class="table table-hover align-middle mb-0">

        <thead class="table-light position-sticky top-0">
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Cutoff</th>
                <th>Date</th>
                <th>Employees</th>
                <th>Status</th>
                <th width="120">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payrolls as $payroll)

                @php
                    $isFirst = \Carbon\Carbon::parse($payroll->payroll_date)->day <= 15;
                @endphp

                <tr class="border-bottom">

                    <td class="fw-semibold text-dark">
                        #{{ $payroll->id }}
                    </td>

                    <td>
                        <span class="badge bg-primary-subtle text-primary">
                            {{ $payroll->employment_types }}
                        </span>
                    </td>

                    <td>
                        <span class="badge {{ $isFirst ? 'bg-primary' : 'bg-success' }}">
                            {{ $payroll->cut_off_period }}
                        </span>
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($payroll->payroll_date)->format('M d, Y') }}
                    </td>

                    <td class="fw-bold">
                        {{ $payroll->employee_count }}
                    </td>

                    <td>
                        <span class="badge bg-success">
                            Approved
                        </span>
                    </td>

                    <td>
                        <a target="_blank"
                           href="{{ route('reports.eme.payroll.view', $payroll->id) }}"
                           class="btn btn-sm btn-outline-primary">
                           View
                        </a>

                        @if($payroll->status !== 'pending')
                            <button
                                wire:click="disapprove({{ $payroll->id }})"
                                class="btn btn-warning btn-sm"
                                title="Pending Payroll">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        @endif
                    </td>

                </tr>

            @endforeach
        </tbody>
    </table>
</div>