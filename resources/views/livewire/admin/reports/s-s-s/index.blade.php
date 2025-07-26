<div>
   <div class="d-flex justify-content-between align-items-center">
        <div>
            <label for="sectionFilter" class="form-label fw-semibold">Filter by Department</label>
            <select id="sectionFilter" wire:model.live="selectedSection" class="form-select">
                <option value="">All Sections</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary px-5 py-3" id="printButton">
            <i class="fa-solid fa-print me-2"></i> PRINT
        </button>
    </div>

    <div class="table-content">
        <div class="table-responsive mt-4 mb-3">
            <table class="table table-bordered mb-0 text-center align-middle" style="min-width: 820px;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Employees</th>
                        <th>Employee (EE) Share</th>
                        <th>Employer (ER) Share</th>
                        <th>EC (ER Only)</th>
                        <th>Total Contributions</th>
                    </tr>
                </thead>
                <tbody class="table-light">
                    <tr>
                        <td>{{ $employee_count }}</td>
                        <td>₱ {{ number_format($total_employee_share, 2) }}</td>
                        <td>₱ {{ number_format($total_employer_share, 2) }}</td>
                        <td>₱ {{ number_format($total_ec, 2) }}</td>
                        <td class="fw-bold bg-success bg-opacity-25">₱ {{ number_format($total_contribution, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @foreach($groupedRecords as $group)
            <h5 class="mt-4" style="font-weight: 800">{{ $group['section'] }}</h5>
            <div class="table-responsive">
                <table class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-start">No.</th>
                            <th class="text-start">SS No.</th>
                            <th class="text-start">Name</th>
                            <th class="text-start">ER</th>
                            <th class="text-start">EE</th>
                            <th class="text-start">EC</th>
                            <th class="text-start">Total</th>
                            <th class="text-start">Monthly Salary</th>
                            <th class="text-start">Status</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @foreach($group['records'] as $record)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $record->personal->sss_no }}</td>
                                <td class="text-start">
                                    {{ trim("{$record->personal->suffix} {$record->personal->firstname} {$record->personal->middlename} {$record->personal->lastname}") }}
                                </td>
                                <td class="text-start">₱ {{ $record->employee_share }}</td>
                                <td class="text-start">₱ {{ $record->employer_share }}</td>
                                <td class="text-start">₱ {{ $record->ec }}</td>
                                <td class="text-start">₱ {{ $record->total }}</td>
                                <td class="text-start">₱ {{ number_format($record->salary, 2) }}</td>
                                <td class="text-start">{{ $record->status }}</td>
                            </tr>
                        @endforeach
                            {{-- Subtotal row --}}
                            <tr class="table-secondary fw-bold">
                                <td colspan="3" class="text-end">Sub Total :</td>
                                <td>{{ number_format($group['subtotal_employee_share'], 2) }}</td>
                                <td>{{ number_format($group['subtotal_employer_share'], 2) }}</td>
                                <td>{{ number_format($group['subtotal_ec'], 2) }}</td>
                                <td>{{ number_format($group['subtotal_total'], 2) }}</td>
                                <td> </td>
                                <td> </td>
                            </tr>
                    </tbody>
                </table>
            </div>
          @endforeach
    </div>

</div>
