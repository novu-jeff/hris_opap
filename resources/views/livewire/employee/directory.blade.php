<div wire:ignore>
    <div class="accordion" id="accordionExample">
        @foreach ($records as $recordIndex => $record)
            @if (isset($record['branch_id']))
                {{-- Branch Listing --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingBranch{{ $record['branch_id'] }}">
                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBranch{{ $record['branch_id'] }}" aria-expanded="{{ $recordIndex === 0 ? 'true' : 'false' }}" aria-controls="collapseBranch{{ $record['branch_id'] }}">
                            {{ $record['branch_name'] }}
                        </button>
                    </h2>
                    <div id="collapseBranch{{ $record['branch_id'] }}" class="accordion-collapse collapse {{ $recordIndex === 0 ? 'show' : '' }}" aria-labelledby="headingBranch{{ $record['branch_id'] }}" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            @foreach ($record['departments'] as $department)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingDepartment{{ $department['department_id'] }}">
                                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDepartment{{ $department['department_id'] }}" aria-expanded="false" aria-controls="collapseDepartment{{ $department['department_id'] }}">
                                            {{ $department['department_name'] }}
                                        </button>
                                    </h2>
                                    <div id="collapseDepartment{{ $department['department_id'] }}" class="accordion-collapse collapse show" aria-labelledby="headingDepartment{{ $department['department_id'] }}" data-bs-parent="#collapseBranch{{ $record['branch_id'] }}">
                                        <div class="accordion-body">
                                            @foreach ($department['sections'] as $section)
                                                {{-- Section Listing --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingSection{{ $section['section_id'] }}">
                                                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSection{{ $section['section_id'] }}" aria-expanded="false" aria-controls="collapseSection{{ $section['section_id'] }}">
                                                            <div class="d-flex justify-content-between w-100 pe-4">
                                                                <div>
                                                                    {{ $section['section_name'] }} 
                                                                </div>
                                                                @php 
                                                                    $empCount = !empty($section['employees']) ? count($section['employees']) : 0;
                                                                @endphp
                                                                <div class="text-muted">
                                                                    {{ $empCount }} Employee{{ $empCount > 1 ? 's' : '' }}
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapseSection{{ $section['section_id'] }}" class="accordion-collapse collapse {{ $recordIndex === 0 ? 'show' : '' }}" aria-labelledby="headingSection{{ $section['section_id'] }}" data-bs-parent="#collapseDepartment{{ $department['department_id'] }}">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                               @if (!empty($section['employees']))
                                                                    @foreach ($section['employees'] as $employee)
                                                                    {{-- Employee Card --}}
                                                                    <div class="col-12 col-md-6 mb-4">
                                                                        <div class="d-lg-flex align-items-center justify-content-center justify-content-lg-start gap-3">
                                                                            <div class="mb-3 mb-lg-0">
                                                                                @php
    $fname = data_get($employee, 'personal.firstname', 'Unknown');
    $lname = data_get($employee, 'personal.lastname', '');
@endphp
                                                                                <img style="width: 80px; height: 80px;"
     src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($fname . ' ' . $lname) }}">
                                                                            </div>
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li>Employee No: <strong>{{ $employee['employee_no'] }}</strong></li>
                                                                                <li>Full Name: <strong>{{ ucwords($fname . ' ' . $lname) }}</strong></li>
                                                                                <li>Email: <strong>{{ data_get($employee, 'account.email', 'N/A') }}</strong></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif (isset($record['group_name']))
                {{-- Unassigned Employees --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingUnassigned">
                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnassigned" aria-expanded="false" aria-controls="collapseUnassigned">
                            {{ $record['group_name'] }}
                        </button>
                    </h2>
                    <div id="collapseUnassigned" class="accordion-collapse collapse show" aria-labelledby="headingUnassigned" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="row">
                                @foreach ($record['employees'] as $employee)
                                    {{-- Employee Card --}}
                                    <div class="col-12 col-md-6 mb-4">
                                        <div class="d-lg-flex align-items-center justify-content-center justify-content-lg-start gap-3">
                                            <div class="mb-3 mb-lg-0">
                                                <img style="width: 80px; height: 80px;"
                                                    src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname']) }}">              
                                            </div>
                                            <ul class="list-unstyled mb-0">
                                                <li>Employee No: <strong>{{ $employee['employee_no'] }}</strong></li>
                                                <li>Full Name: <strong>{{ ucwords($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname']) }}</strong></li>
                                                <li>Email: <strong>{{ $employee['account']['email'] }}</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
