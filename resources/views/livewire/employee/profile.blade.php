<div>
    <div class="card mb-4">
        <div class="card-body  px-5">
                <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-3 py-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'details']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'details' ? 'active' : ''}}" id="pills-details-tab" role="tab" aria-controls="pills-details" aria-selected="{{$form == 'details' ? 'true' : 'false'}}">Employee Details</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'family']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'family' ? 'active' : ''}}" id="pills-family-tab" role="tab" aria-controls="pills-family" aria-selected="{{$form == 'family' ? 'true' : 'false'}}">Family Background</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'children']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'children' ? 'active' : ''}}" id="pills-children-tab" role="tab" aria-controls="pills-children" aria-selected="{{$form == 'children' ? 'true' : 'false'}}">Children</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'education']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'education' ? 'active' : ''}}" id="pills-education-tab" role="tab" aria-controls="pills-education" aria-selected="{{$form == 'education' ? 'true' : 'false'}}">Education Information</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'employment']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'employment' ? 'active' : ''}}" id="pills-employment-tab" role="tab" aria-controls="pills-history" aria-selected="{{$form == 'employment' ? 'true' : 'false'}}">Employment History</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'civil-service']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'civil-service' ? 'active' : ''}}" id="pills-civil-service-tab" role="tab" aria-controls="pills-civil-service" aria-selected="{{$form == 'civil-service' ? 'true' : 'false'}}">Civil Service Eligibility</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'trainings']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'trainings' ? 'active' : ''}}" id="pills-trainings-tab" role="tab" aria-controls="pills-trainings" aria-selected="{{$form == 'trainings' ? 'true' : 'false'}}">Trainings</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'other-works']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'other-works' ? 'active' : ''}}" id="pills-other-works-tab" role="tab" aria-controls="pills-others" aria-selected="{{$form == 'other-works' ? 'true' : 'false'}}">Other Voluntary Works</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('employee.profile', ['form' => 'skills']) }}" class="px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'skills' ? 'active' : ''}}" id="pills-skills-tab" role="tab" aria-controls="pills-skills" aria-selected="{{$form == 'skills' ? 'true' : 'false'}}">Skills or Hobbies</a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                @php
                    $viewForms = [
                        'details' => 'employee.profile.details',
                        'family' => 'employee.profile.family',
                        'children' => 'employee.profile.children',
                        'education' => 'employee.profile.education',
                        'employment' => 'employee.profile.employment',
                        'civil-service' => 'employee.profile.civil-service',
                        'trainings' => 'employee.profile.trainings',
                        'other-works' => 'employee.profile.other-works',
                        'skills' => 'employee.profile.skills',
                    ];

                    $view = $viewForms[$form];
                @endphp
                
                @livewire($view)
            </div>
        </div>
    </div>
</div>
