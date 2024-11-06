<div wire:poll>
    
    <div class="text-uppercase">
        <h4 class="mb-1 fw-bold">Branch: {{ $records['branch']['branch_name'] }}</h4>
        <h4 class="mb-1 fw-bold">Department: {{ $records['department']['department_name'] }}</h4>
    </div>
    
    <div class="mt-3">
        @foreach ($records['positions'] as $position)
            <div class="mt-5 mb-2">
                <h5 class="text-uppercase fw-bold">Position: {{ $position['position_name'] }} ({{ count($position['employees']) }} Employees)</h5>
                <div class="row mt-4">
                    @foreach ($position['employees'] as $employee)
                        <div class="col-12 col-md-6 mb-4">
                            <div class="d-md-flex align-items-center gap-3">
                                <div>
                                    <img src="{{ $employee['personal']['profile'] ? Storage::url('employee/users/' . $employee['personal']['employee_id'] . '/' . $employee['personal']['profile']) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10' }}" 
                                         style="width: 120px; height: 120px;">
                                </div>
                                    <ul class="list-unstyled mb-0">
                                    <li>Employee ID: #<strong>{{ format_id($employee['id'], 6) }}</strong></li>
                                    <li>Biometrics ID: <strong>{{ $employee['biometrics_id'] }}</strong></li>
                                    <li>Full Name: <strong>{{ ucwords($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname']) }}</strong></li>
                                    <li>Email: <strong>{{ $employee['account']['email'] }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                    @if(count($records['positions']) > 1)
                        <hr class="mt-4 mb-0">
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
