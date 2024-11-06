<div wire:poll>
    <div class="accordion" id="accordionExample">
        @foreach ($records as $index => $branch) 
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $branch['branch_id'] }}">
                    <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $branch['branch_id'] }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $branch['branch_id'] }}">
                        {{ $branch['branch_name'] }}
                    </button>
                </h2>
                <div id="collapse{{ $branch['branch_id'] }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $branch['branch_id'] }}" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="accordion" id="accordionDepartments{{ $branch['branch_id'] }}">
                            @foreach ($branch['departments'] as $departmentId => $department) 
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingDepartment{{ $department['department_id'] }}">
                                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDepartment{{ $department['department_id'] }}" aria-expanded="false" aria-controls="collapseDepartment{{ $department['department_id'] }}">
                                            {{ $department['department_name'] }}
                                        </button>
                                    </h2>
                                    <div id="collapseDepartment{{ $department['department_id'] }}" class="accordion-collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="headingDepartment{{ $department['department_id'] }}" data-bs-parent="#accordionDepartments{{ $branch['branch_id'] }}">
                                        <div class="accordion-body">
                                            @foreach ($department['positions'] as $positionId => $position) 
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingPosition{{ $positionId }}">
                                                        <button class="accordion-button text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePosition{{ $positionId }}" aria-expanded="false" aria-controls="collapsePosition{{ $positionId }}">
                                                            {{ $position['position_name'] }} ({{ count($position['employees']) }} Employees)
                                                        </button>
                                                    </h2>
                                                    <div id="collapsePosition{{ $positionId }}" class="accordion-collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="headingPosition{{ $positionId }}" data-bs-parent="#collapseDepartment{{ $department['department_id'] }}">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                @foreach ($position['employees'] as $index => $employee)
                                                                    <div class="col-12 col-md-6 mb-4">
                                                                        <div class="d-lg-flex align-items-center justify-content-center justify-content-lg-start gap-3">
                                                                            <div class="mb-3 mb-lg-0">
                                                                                <img src="{{
                                                                                    $employee['personal']['profile'] ? Storage::url('employee/users/'.$employee['personal']['employee_id'].'/'.$employee['personal']['profile']) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                                                                                }}" style="width: 120px; height: 120px;">
                                                                            </div>
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li>Employee ID: <strong>{{ format_id($employee['id'], 6) }}</strong></li>
                                                                                <li>Biometrics ID: <strong>{{ $employee['biometrics_id'] }}</strong></li>
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
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
