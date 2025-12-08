<div wire:poll>
    <div class="text-uppercase">
        <h5 class="mb-2 fw-bold">OPAPRU Central Office: <span class="ms-1 text-decoration-underline">{{ $records['branch']['branch_name'] }}</span></h5>
        <h5 class="mb-2 fw-bold">Department: <span class="ms-1 text-decoration-underline">{{ $records['department']['department_name'] }}</span></h5>
        <h5 class="mb-2 fw-bold">Section: <span class="ms-1 text-decoration-underline">{{ $records['section']['section_name'] }}</span></h5>
    </div>
    
    <hr class="mt-5 mb-5">
    <div>
        @foreach ($records['positions'] as $position)
            <div class="mt-4 mb-2">
                <div class="d-flex justify-content-between">
                    <h5 class="text-uppercase fw-bold">Position: {{ $position['position_name'] }}</h5>
                    <h5 class="text-uppercase text-muted fw-bold">({{ count($position['employees']) }} Employee{{ count($position['employees']) > 1 ? 's' : '' }})</h5>
                </div>
                <div class="row">
                    @foreach ($position['employees'] as $employee)
                        <div class="col-12 col-md-6 mb-4">
                            <div class="d-md-flex align-items-center gap-3">
                                <div>
                                    @php
                                        $profile = $employee['personal']['profile'] ?? null;
                                    @endphp

                                    @if($profile && file_exists(public_path('storage/' . $profile)))
                                        <img src="{{ asset('storage/' . $profile) }}" 
                                             alt="Profile Photo" 
                                             style="width: 60px; height: 100px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <img src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname']) }}" 
                                             alt="Avatar" 
                                             style="width: 60px; height: 100px; object-fit: cover; border-radius: 5px;">
                                    @endif
                                </div>
                                <ul class="list-unstyled mb-0 fs-6">
                                    <li>Full Name: <strong>{{ ucwords($employee['personal']['firstname'] . ' ' . $employee['personal']['lastname']) }}</strong></li>
                                    <li>Email: <strong>{{ $employee['account']['email'] ?? $employee['account']['email_id'] ?? '' }}</strong></li>
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
