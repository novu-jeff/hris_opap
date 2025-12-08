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
                        <th>Grand Total</th>
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
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-start">Pagibig No.</th>
                        <th class="text-start">Employee No</th>
                        <th class="text-start">Lastname</th>
                        <th class="text-start">Firstname</th>
                        <th class="text-start">Middlename</th>
                        <th class="text-start">Employee Share</th>
                        <th class="text-start">Employer Share</th>
                        <th class="text-start">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['records'] as $employee)
                        <tr>
                            <td class="text-start">{{ $employee->personal->pagibig_no  ?? 'N/A' }}</td>
                            <td class="text-start">{{ $employee->employee_no ?? ''   }}</td>
                            <td class="text-start">{{ $employee->personal->lastname ?? '' }}</td>
                            <td class="text-start">{{ $employee->personal->firstname ?? '' }}</td>
                            <td class="text-start">{{ $employee->personal->middlename ?? '' }}</td>
                            <td>{{ number_format($employee->employee_share, 2) }}</td>
                            <td>{{ number_format($employee->employer_share, 2) }}</td>
                            <td>{{ number_format($employee->total, 2) }}</td>
                        </tr>
                    @endforeach
                    {{-- Subtotal row --}}
                    <tr class="table-secondary fw-bold">
                        <td colspan="5" class="text-end">Sub Total :</td>
                        <td>{{ number_format($group['subtotal_employee_share'], 2) }}</td>
                        <td>{{ number_format($group['subtotal_employer_share'], 2) }}</td>
                        <td>{{ number_format($group['subtotal_total'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
   
</div>
