<div wire:poll>
    
    <div class="text-uppercase">
        <h5 class="mb-2 fw-bold">Branch: <span class="ms-1 text-decoration-underline">{{ $records['branch']['branch_name'] }}</span></h5>
        <h5 class="mb-2 fw-bold">Department: <span class="ms-1 text-decoration-underline">{{ $records['department']['department_name'] }}</span></h5>
        <h5 class="mb-2 fw-bold">Section: <span class="ms-1 text-decoration-underline">{{ $records['section']['section_name'] }}</span></h5>
    </div>
    
    <div class="mt-3">
        @foreach ($records['positions'] as $position)
            <div class="mt-5 mb-2">
                <div class="d-flex justify-content-between">
                    <h5 class="text-uppercase fw-bold">Position: {{ $position['position_name'] }}</h5>
                    <h5 class="text-uppercase text-muted fw-bold">({{ count($position['employees']) }} Employee{{ count($position['employees']) > 1 ? 's' : '' }})</h5>
                </div>
                <div class="row mt-4">
                    @foreach ($position['employees'] as $employee)
                        <div class="col-12 col-md-6 mb-4">
                            <div class="d-md-flex align-items-center gap-3">
                                <div>
                                    <img style="width: 100px; height: 100px;"
                                        src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname']) }}">              
                                </div>
                                    <ul class="list-unstyled mb-0">
                                    <li>Employee No: <strong>{{ $employee['employee_no'] }}</strong></li>
                                    <li>Biometrics ID: <strong>{{ $employee['bsd_no'] }}</strong></li>
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
