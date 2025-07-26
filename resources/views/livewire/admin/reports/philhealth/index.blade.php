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
                        <th>Total Contributions</th>
                    </tr>
                </thead>
                <tbody class="table-light">
                    <tr>
                        <td>{{ $employee_count }}</td>
                        <td>₱ {{ number_format($total_employee_share, 2) }}</td>
                        <td>₱ {{ number_format($total_employer_share, 2) }}</td>
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
                            <th class="text-start">PIN</th>
                            <th class="text-start">Lastname</th>
                            <th class="text-start">Suffix</th>
                            <th class="text-start">Firstname</th>
                            <th class="text-start">Middlename</th>
                            <th class="text-start">Birthday</th>
                            <th class="text-start">Monthly Salary</th>
                            <th class="text-start">ER Share</th>
                            <th class="text-start">EE Share</th>
                            <th class="text-start">PhilHealth Total</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @foreach($group['records'] as $record)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $record->personal->philhealth_no }}</td>
                                <td>{{ $record->personal->lastname }}</td>
                                <td>{{ $record->personal->suffix ?? 'N/A' }}</td>
                                <td>{{ $record->personal->firstname }}</td>
                                <td>{{ $record->personal->middlename }}</td>
                                <td>{{ $record->personal->birthday }}</td>
                                <td>₱ {{ number_format($record->salary, 2) }}</td>
                                <td>₱{{ $record->employee_share }}</td>
                                <td>₱{{ $record->employer_share }}</td>
                                <td>₱{{ $record->total }}</td>
                            </tr>
                        @endforeach
                            <tr class="table-secondary fw-bold">
                                <td colspan="8" class="text-end">Sub Total :</td>
                                <td>{{ number_format($group['subtotal_employee_share'], 2) }}</td>
                                <td>{{ number_format($group['subtotal_employer_share'], 2) }}</td>
                                <td>{{ number_format($group['subtotal_total'], 2) }}</td>
                            </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
   
   
</div>
