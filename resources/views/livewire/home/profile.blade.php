<div class="profile">
    <div class="row">
        <div class="col-12 col-md-4 col-lg-4 mb-4">
            <div class="sticky">
                <div class="profile-section">
                    <div class="d-flex justify-content-center">
                        <div class="img-content" wire:ignore.self>
                            <img src="{{
                                $record->image ? Storage::url('applicant/users/'.$record->id.'/'.$record->image) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                            }}" alt="">
                            <button class="edit-icon btn btn-primary" data-bs-toggle="modal" data-bs-target="#update-profile-image-modal">
                                <i class="fa-solid fa-camera"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="info">
                    <h3>{{$record->firstname . ' ' . $record->lastname}}</h3>
                    <p class="m-0 text-uppercase">{{$record->city . ', ' . $record->province}}</p>
                    <p class="m-0">{{$record->email}}</p>
                </div>
                <hr>
                <div class="pills-section">
                    <div class="d-flex align-items-start">
                        <div class="nav flex-column nav-pills w-100 " id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link text-start {{$activeTab == 'profile' ? 'active' : ''}}" wire:click.prevent="setActiveTab('profile')" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">My Profile</button>
                            <button class="nav-link text-start {{$activeTab == 'interview' ? 'active' : ''}}" wire:click.prevent="setActiveTab('interview')" id="v-pills-interview-tab" data-bs-toggle="pill" data-bs-target="#v-pills-interview" type="button" role="tab" aria-controls="v-pills-interview" aria-selected="false">Interview</button>
                            <button class="nav-link text-start {{$activeTab == 'placement' ? 'active' : ''}}" wire:click.prevent="setActiveTab('placement')" id="v-pills-placement-tab" data-bs-toggle="pill" data-bs-target="#v-pills-placement" type="button" role="tab" aria-controls="v-pills-placement" aria-selected="false">Placement</button>
                            <button class="nav-link text-start {{$activeTab == 'onboarding' ? 'active' : ''}}" wire:click.prevent="setActiveTab('onboarding')" id="v-pills-onboarding-tab" data-bs-toggle="pill" data-bs-target="#v-pills-onboarding" type="button" role="tab" aria-controls="v-pills-onboarding" aria-selected="false">OnBoarding</button>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="skills-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Skills
                        </h5>
                        <button class="edit-icon btn btn-primary" data-bs-toggle="modal" data-bs-target="#update-profile-skills-modal">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </div>
                    <div class="skill-content">
                        @if ($record->skills->isEmpty())
                            <div class="alert alert-info w-100 text-center text-uppercase fw-bold">No skills listed</div>
                        @else
                            @foreach ($record->skills as $skill)
                                <div class="skill-box">
                                    {{$skill->skills->name}}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8 col-lg-8 mb-4">
            <div class="tab-content" id="v-pills-tabContent">
                <div wire:ignore.self class="tab-pane fade {{$activeTab == 'profile' ? 'show active' : ''}}" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab" tabindex="0">
                    <div class="card px-3 pt-4 pb-5 profile-content">
                        <div class="card-header bg-transparent border-0">
                            <div>
                                <h4>Profile</h4>
                                <p>
                                    Enhance your career journey by updating your employment details
                                    and personal information for a more personalized experience.
                                </p>
                            </div>
                            <div class="actions text-uppercase mt-3">
                                <button class="btn btn-outline-primary text-uppercase fw-bold px-5 py-3" data-bs-toggle="modal" data-bs-target="#update-profile-modal">Edit Profile</button>
                            </div>
                        </div>
                        <hr>
                        <div class="card-body">
                            <div>
                                <h4>My Information</h4>
                                <p>
                                    Keep your information updated to receive the most relevant content
                                    and engage in meaningful conversations.
                                </p>
                            </div>
                            <div class="mt-4">
                                <h6>Basic Information</h6>
                                <ul class="list-unstyled">
                                    <li>Full Name: <span class="text-uppercase">{{format_name($record->firstname, $record->middlename, $record->lastname)}}</span></li>
                                    <li>Birth Day: <span class="text-uppercase">{{format_date($record->birthday, 'date_string')}}</span></li>
                                    <li>Age: <span class="text-uppercase">{{format_date($record->birthday, 'age')}} years old</span></li>
                                    <li>Date Joined: <span class="text-uppercase">{{format_date($record->date_joined, 'day_date_string')}}</span></li>
                                </ul>
                            </div>
                            <div class="mt-4">
                                <h6>Education Information</h6>
                                <ul class="list-unstyled">
                                    <li>Highest Attainment: <span class="text-uppercase">{{$record->level ? $record->level : 'No info'}}</span></li>
                                    <li>School Name: <span class="text-uppercase">{{$record->school_name ? $record->school_name : 'No info'}}</span></li>
                                    <li>Course: <span class="text-uppercase">{{$record->course ? $record->course : 'No info'}}</span></li>
                                    <li>School Year: <span class="text-uppercase">{{ $record->started && $record->finished ? format_date($record->started, 'date_string') . ' - ' . format_date($record->finished, 'date_string') : 'No info' }}</span></li>
                                </ul>
                            </div> 
                            <div class="mt-4">
                                <h6>Resume</h6>
                                <p>Update your resume to streamline your job application and increase visibility to potential employers.</p>
                                <button class="w-100 border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#update-profile-resume-modal">
                                    <div class="droparea">
                                        <div>
                                            <div class="m-auto text-center icon">
                                                <i class="fa-solid fa-upload fa-bounce"></i>
                                            </div>
                                            <div class="text-center">
                                                <div>Upload resume here</div>
                                                <small>(only accepts doc, docx, and pdf file)</small>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                                <p>Current Resume: 
                                    @if ($record->resume)
                                        <a target="_blank" href="{{ Storage::url('applicant/users/' . $record->id . '/' . $record->resume) }}">
                                            {{ $record->resume }}
                                        </a>
                                    @else
                                        No uploaded resume
                                    @endif
                                </p>
                            </div>
                            <div class="mt-4">
                                <h6>Job Information</h6>
                                <div class="alert alert-info mt-3 text-uppercase fw-bold text-center">Currenly Unemployed</div>
                                {{-- <ul class="list-unstyled">
                                    <li>Employment Status: Unemployed</li>
                                    <li>Job Position: Quality Assurance</li>
                                    <li>Job Location: Pasig, City</li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane fade {{$activeTab == 'interview' ? 'show active' : ''}}" id="v-pills-interview" role="tabpanel" aria-labelledby="v-pills-interview-tab" tabindex="0">
                    <div class="card px-3 pt-4 pb-5 profile-content">
                        <div class="card-header bg-transparent border-0">
                            <h4>My Interviews</h4>
                            <p>
                                Here are the interview questions you need to answer.
                            </p>
                        </div>
                        <hr>
                        <div class="card-body">
                           @if (!$record->applied->isEmpty())
                                @foreach ($record->applied as $applied)
                                    @if ($applied->status != 'hired')
                                        <div class="card px-2" style="border: none">
                                            <div class="card-header border-0 bg-transparent">
                                                <div class="position-title">
                                                    <h4 class="m-0 text-uppercase">{{$applied->job->position}}</h4>
                                                </div>
                                                <div class="company-info">
                                                    <p class="m-0 text-uppercase">{{$applied->job->company_name}}</p>
                                                    <p class="m-0 text-uppercase">{{$applied->job->location . ' • ' . str_replace('-', ' ', $applied->job->setup) . ' • ' . str_replace('-', ' ', $applied->job->type)}}</p>
                                                </div>
                                                <div class="salary">
                                                    <p class="m-0 text-uppercase">{{money_format($applied->job->min_salary) . ' - ' . money_format($applied->job->max_salary)}} per month</p>
                                                </div>
                                                <div class="date-posted">
                                                    <p class="m-0">
                                                        Posted {{relative_time($applied->job->created_at, 'hours ago')}}
                                                    </p>
                                                </div>
                                                <div class="actions mt-4 d-flex gap-3 justify-content-start">
                                                    @if ($applied->status == 'interview' && !$applied->isInterviewResponded)
                                                        <a href="{{route('interview-respond', ['job_id' => $applied->job->id, 'interview_id' => $applied->interview[0]->job_interview_id])}}" class="btn btn-primary d-flex align-items-center gap-2 text-uppercase px-4 py-3 fw-bold">
                                                            <span>
                                                                Answer Interview
                                                            </span>
                                                        </a>
                                                    @elseif($applied->isInterviewResponded)
                                                        <button type="button" class="btn btn-outline-warning d-flex align-items-center gap-2 text-uppercase px-4 py-3 fw-bold">
                                                            <span>
                                                                Already Answered
                                                            </span>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-2 text-uppercase px-4 py-3 fw-bold">
                                                            <span>
                                                                Interview Unavailable
                                                            </span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif
                                @endforeach
                            @else
                                <div class="alert alert-info text-center text-uppercase">Currently No Interview</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane fade {{$activeTab == 'placement' ? 'show active' : ''}}" id="v-pills-placement" role="tabpanel" aria-labelledby="v-pills-placement-tab" tabindex="0">
                    <div class="card px-3 pt-4 pb-5 profile-content">
                        <div class="card-header bg-transparent border-0">
                            <h4>Your Job Offers</h4>
                            <p>
                                Once your interviews are finished, if HR considers you a suitable candidate, your job offers will be provided and shown here.
                            </p>
                        </div>
                        <hr>
                        <div class="card-body">
                            @php
                                $filterRecords = $record->applied->filter(fn($applied) => 
                                    in_array($applied->status, ['placement', 'onboarding']) && !is_null($applied->offer)
                                );
                            @endphp

                            @if ($filterRecords->isEmpty())
                                <div class="alert alert-info text-center text-uppercase">Currently No Job Offers</div>
                            @else
                                @foreach ($filterRecords as $applied)
                                    <div class="card px-2" style="border: none">
                                        <div class="card-header border-0 bg-transparent">
                                            <div class="position-title">
                                                <h4 class="m-0 text-uppercase">{{ $applied->job->position }}</h4>
                                            </div>
                                            <div class="company-info">
                                                <p class="m-0 text-uppercase">{{ $applied->job->company_name }}</p>
                                                <p class="m-0 text-uppercase">
                                                    {{ $applied->job->location }} • {{ str_replace('-', ' ', ucwords($applied->job->setup)) }} • {{ str_replace('-', ' ', ucwords($applied->job->type)) }}
                                                </p>
                                            </div>
                                            <div class="salary">
                                                <p class="m-0 text-uppercase">{{ money_format($applied->job->min_salary) . ' - ' . money_format($applied->job->max_salary) }} per month</p>
                                            </div>
                                            <div class="date-posted">
                                                <p class="m-0">
                                                    Posted {{ relative_time($applied->job->created_at, 'hours ago') }}
                                                </p>
                                            </div>
                                            <div class="actions mt-4 d-flex gap-3 justify-content-start">
                                                <button wire:click="download_offer({{ $applied->id }})" class="btn btn-primary d-flex align-items-center gap-2 text-uppercase px-4 py-3 fw-bold">
                                                    <span>Download Job Offer</span>
                                                </button>

                                                @if (!$applied->isSignedJobOffer)
                                                    <a href="{{ route('upload-signed-offer', ['job_id' => $applied->job->id]) }}" class="btn btn-outline-primary d-flex align-items-center gap-2 text-uppercase px-4 py-3 fw-bold">
                                                        <span>Upload Signed Job Offer</span>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2 text-uppercase px-4 py-3 fw-bold">
                                                        <span>Already Signed</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane fade {{$activeTab == 'onboarding' ? 'show active' : ''}}" id="v-pills-onboarding" role="tabpanel" aria-labelledby="v-pills-onboarding-tab" tabindex="0">
                    <div class="card px-3 pt-4 pb-5 profile-content">
                        <div class="card-header bg-transparent border-0">
                            <h4>Your Requirements</h4>
                            <p>
                                Once you've signed your job offer, HR will require your initial documents.
                            </p>
                        </div>
                        <hr>
                        <div class="card-body" wire:ignore>
                            @php
                                    $filterRecords = $record->applied->filter(fn($applied) => 
                                    in_array($applied->status, ['placement', 'onboarding']) && !is_null($applied->offer) && $applied->isSignedJobOffer
                                );
                            @endphp

                            @if ($filterRecords->isEmpty())
                                <div class="alert alert-info text-center text-uppercase">Not ready for any onboarding</div>
                            @else
                                @foreach ($filterRecords as $applied)
                                    <div class="card px-2" style="border: none">
                                        <div class="card-header border-0 bg-transparent">
                                            <div class="position-title">
                                                <h4 class="m-0 text-uppercase">{{$applied->job->position}}</h4>
                                            </div>
                                            <div class="company-info">
                                                <p class="m-0 text-uppercase">{{$applied->job->company_name}}</p>
                                                <p class="m-0 text-uppercase">{{$applied->job->location . ' • ' . str_replace('-', ' ', $applied->job->setup) . ' • ' . str_replace('-', ' ', $applied->job->type)}}</p>
                                            </div>
                                            <div class="salary">
                                                <p class="m-0 text-uppercase">{{money_format($applied->job->min_salary) . ' - ' . money_format($applied->job->max_salary)}} per month</p>
                                            </div>
                                            <div class="date-posted">
                                                <p class="m-0">
                                                    Posted {{relative_time($applied->job->created_at, 'hours ago')}}
                                                </p>
                                            </div>
                                            <div class="actions mt-4 d-flex gap-3 justify-content-start">
                                                <a href="{{route('upload-requirements', ['job_id' => $applied->job->id])}}" class="btn btn-outline-primary d-flex align-items-center gap-2 text-uppercase px-4 py-3 fw-bold">
                                                    <span>
                                                        Upload Requirements
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div wire:ignore>
        <div>
            @livewire('home.modals.update-profile')
        </div>
        <div>
            @livewire('home.modals.update-profile-resume')
        </div>
        <div>
            @livewire('home.modals.update-profile-image')
        </div>
        <div>
            @livewire('home.modals.update-profile-skills')
        </div>
    </div>

</div>