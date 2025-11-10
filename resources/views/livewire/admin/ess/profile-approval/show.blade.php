<div>
    <div class="card mb-4">
        <div class="card-body  px-5">
            <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-3 py-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'personal']) }}" class="{{in_array('personal', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'personal' ? 'active' : ''}}" id="pills-personal-tab" role="tab" aria-controls="pills-personal" aria-selected="{{$form == 'personal' ? 'true' : 'false'}}">I. Personal Information</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'family']) }}" class="{{in_array('family', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'family' ? 'active' : ''}}" id="pills-family-tab" role="tab" aria-controls="pills-family" aria-selected="{{$form == 'family' ? 'true' : 'false'}}">II. Family Background (A)</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'children']) }}" class="{{in_array('children', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'children' ? 'active' : ''}}" id="pills-children-tab" role="tab" aria-controls="pills-children" aria-selected="{{$form == 'children' ? 'true' : 'false'}}">II. Family Background (B)</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'education']) }}" class="{{in_array('education', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'education' ? 'active' : ''}}" id="pills-education-tab" role="tab" aria-controls="pills-education" aria-selected="{{$form == 'education' ? 'true' : 'false'}}">III. Educational Background</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'civil-service']) }}" class="{{in_array('civil-service', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'civil-service' ? 'active' : ''}}" id="pills-civil-service-tab" role="tab" aria-controls="pills-civil-service" aria-selected="{{$form == 'civil-service' ? 'true' : 'false'}}">IV. Civil Service Eligibility</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'employment-history']) }}" class="{{in_array('employment-history', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'employment-history' ? 'active' : ''}}" id="pills-employment-tab" role="tab" aria-controls="pills-history" aria-selected="{{$form == 'employment' ? 'true' : 'false'}}">V. Work Experience</a>
                </li>
                    <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'other-works']) }}" class="{{in_array('other-works', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'other-works' ? 'active' : ''}}" id="pills-other-works-tab" role="tab" aria-controls="pills-others" aria-selected="{{$form == 'other-works' ? 'true' : 'false'}}">VI. Voluntary Work or Involvement in Civic / Non-Government / People / Voluntary Organizations</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'trainings']) }}" class="{{in_array('trainings', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'trainings' ? 'active' : ''}}" id="pills-trainings-tab" role="tab" aria-controls="pills-trainings" aria-selected="{{$form == 'trainings' ? 'true' : 'false'}}">VII. Learning and Development (L&D) Interventions / Training Programs Attended</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('ess.approval-profile.show', ['employee_no' => $employee_no, 'form' => 'skills']) }}" class="{{in_array('skills', $tabsHasChanges) ? 'tabsHasChanges' : ''}} px-4 py-2 text-uppercase fw-bold nav-link {{$form == 'skills' ? 'active' : ''}}" id="pills-skills-tab" role="tab" aria-controls="pills-skills" aria-selected="{{$form == 'skills' ? 'true' : 'false'}}">VIII. Skills or Hobbies</a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                @php
                    $viewForms = [
                        'personal' => 'admin.ess.profile-approval.profile.personal',
                        'family' => 'admin.ess.profile-approval.profile.family',
                        'children' => 'admin.ess.profile-approval.profile.children',
                        'education' => 'admin.ess.profile-approval.profile.education',
                        'employment-history' => 'admin.ess.profile-approval.profile.employment',
                        'civil-service' => 'admin.ess.profile-approval.profile.civil-service',
                        'trainings' => 'admin.ess.profile-approval.profile.trainings',
                        'other-works' => 'admin.ess.profile-approval.profile.other-works',
                        'skills' => 'admin.ess.profile-approval.profile.skills',
                    ];

                    $view = $viewForms[$form];
                @endphp
                <hr class="pt-2">
                <div class="mt-4">
                    @livewire($view, ['employee_no' => $employee_no, 'form' => $form])
                </div>
                <div class="card-footer d-flex gap-3 justify-content-end bg-transparent border-0 mt-5 pb-3">
                    <button type="button" wire:click="disapproved" class="btn btn-outline-danger px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="disapproved">Disapprove <i class="fa-solid fa-thumbs-down ms-2"></i></span>
                        <span wire:loading wire:target="disapproved">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                    <button type="button" wire:click="approved" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="approved">Approve <i class="fa-solid fa-thumbs-up ms-2"></i></i></span>
                        <span wire:loading wire:target="approved">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
