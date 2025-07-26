<div>
    <div class="d-flex justify-content-end mb-5 gap-3">
        <a href="{{route('hris.index')}}" class="btn btn-outline-primary px-5 py-3 text-uppercase">Go Back</a>
    </div>
    <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-3 py-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'information']) }}" class="{{in_array('information', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'information' ? 'active' : ''}}" id="pills-details-tab" role="tab" aria-controls="pills-details" aria-selected="{{$form == 'information' ? 'true' : 'false'}}">Employee Information</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'personal']) }}" class="{{in_array('personal', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'personal' ? 'active' : ''}}" id="pills-details-tab" role="tab" aria-controls="pills-details" aria-selected="{{$form == 'personal' ? 'true' : 'false'}}">I. Personal Information</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'family']) }}" class="{{in_array('family', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'family' ? 'active' : ''}}" id="pills-family-tab" role="tab" aria-controls="pills-family" aria-selected="{{$form == 'family' ? 'true' : 'false'}}">II. Family Background (A)</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'children']) }}" class="{{in_array('children', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'children' ? 'active' : ''}}" id="pills-children-tab" role="tab" aria-controls="pills-children" aria-selected="{{$form == 'children' ? 'true' : 'false'}}">II. Family Background (B)</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'education']) }}" class="{{in_array('education', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'education' ? 'active' : ''}}" id="pills-education-tab" role="tab" aria-controls="pills-education" aria-selected="{{$form == 'education' ? 'true' : 'false'}}">III. Educational Background</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'civil-service']) }}" class="{{in_array('civil-service', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'civil-service' ? 'active' : ''}}" id="pills-civil-service-tab" role="tab" aria-controls="pills-civil-service" aria-selected="{{$form == 'civil-service' ? 'true' : 'false'}}">IV. Civil Service Eligibility</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'employment-history']) }}" class="{{in_array('employment-history', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'employment-history' ? 'active' : ''}}" id="pills-employment-tab" role="tab" aria-controls="pills-history" aria-selected="{{$form == 'employment' ? 'true' : 'false'}}">V. Work Experience</a>
        </li>
            <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'other-works']) }}" class="{{in_array('other-works', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'other-works' ? 'active' : ''}}" id="pills-other-works-tab" role="tab" aria-controls="pills-others" aria-selected="{{$form == 'other-works' ? 'true' : 'false'}}">VI. Voluntary Work or Involvement in Civic / Non-Government / People / Voluntary Organizations</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'trainings']) }}" class="{{in_array('trainings', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'trainings' ? 'active' : ''}}" id="pills-trainings-tab" role="tab" aria-controls="pills-trainings" aria-selected="{{$form == 'trainings' ? 'true' : 'false'}}">VII. Learning and Development (L&D) Interventions / Training Programs Attended</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'skills']) }}" class="{{in_array('skills', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'skills' ? 'active' : ''}}" id="pills-skills-tab" role="tab" aria-controls="pills-skills" aria-selected="{{$form == 'skills' ? 'true' : 'false'}}">VIII. Skills or Hobbies</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('hris.show', ['employee_no' => $employee_no, 'form' => 'account']) }}" class="{{in_array('information', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'account' ? 'active' : ''}}" id="pills-details-tab" role="tab" aria-controls="pills-details" aria-selected="{{$form == 'account' ? 'true' : 'false'}}">IX. Employee Account</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        @php
            $viewForms = [
                'information' => 'admin.hris.profile.information',
                'personal' => 'admin.hris.profile.personal',
                'family' => 'admin.hris.profile.family',
                'children' => 'admin.hris.profile.children',
                'education' => 'admin.hris.profile.education',
                'employment-history' => 'admin.hris.profile.employment-history',
                'civil-service' => 'admin.hris.profile.civil-service',
                'trainings' => 'admin.hris.profile.trainings',
                'other-works' => 'admin.hris.profile.other-works',
                'skills' => 'admin.hris.profile.skills',
                'account' => 'admin.hris.profile.account',
            ];

            $view = $viewForms[$form];
        @endphp
        <hr class="pt-2">
        <div style="width: fit-content; margin: auto !important;" class="px-5 py-2 text-white bg-info text-uppercase fw-bold text-center my-4 mb-5">Note: Please save your updated data before leaving the current tab.</div>
        <div class="mt-4">
            @livewire($view, ['employee_no' => $employee_no, 'form' => $form])
        </div>
    </div>
</div>
