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
                            <button class="edit-icon" data-bs-toggle="modal" data-bs-target="#update-profile-image-modal">
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
                            <button wire:ignore class="nav-link text-start active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">My Profile</button>
                            <button wire:ignore class="nav-link text-start" id="v-pills-interview-tab" data-bs-toggle="pill" data-bs-target="#v-pills-interview" type="button" role="tab" aria-controls="v-pills-interview" aria-selected="false">Interview</button>
                            <button wire:ignore class="nav-link text-start" id="v-pills-onboarding-tab" data-bs-toggle="pill" data-bs-target="#v-pills-onboarding" type="button" role="tab" aria-controls="v-pills-onboarding" aria-selected="false">On Boarding</button>
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
                <div wire:ignore.self class="tab-pane fade " id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab" tabindex="0">
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
                            <div class="mt-4" wire:ignore>
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
                                <p>Current Resume: <a target="_blank" href="{{Storage::url('applicant/users/'.$record->id.'/'.$record->resume)}}">{{$record->resume}}</a></p>
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
                <div wire:ignore.self class="tab-pane fade show active" id="v-pills-interview" role="tabpanel" aria-labelledby="v-pills-interview-tab" tabindex="0">
                    <div class="card px-3 pt-4 pb-5 profile-content">
                        <div class="card-header bg-transparent border-0">
                            <h4>My Interviews</h4>
                            <p>
                                Here are the interview questions you need to answer.
                            </p>
                        </div>
                        <hr>
                        <div class="card-body">
                            @foreach ($record->applied as $record)
                                @if ($record->status === 'interview')
                                    <div class="card px-2" style="border: none">
                                        <div class="card-header border-0 bg-transparent">
                                            <div class="position-title">
                                                <h4 class="m-0 text-uppercase">{{$record->job->position}}</h4>
                                            </div>
                                            <div class="company-info">
                                                <p class="m-0 text-uppercase">{{$record->job->company_name}}</p>
                                                <p class="m-0 text-uppercase">{{$record->job->location . ' • ' . str_replace('-', ' ', $record->job->setup) . ' • ' . str_replace('-', ' ', $record->job->type)}}</p>
                                            </div>
                                            <div class="salary">
                                                <p class="m-0 text-uppercase">{{money_format($record->job->min_salary) . ' - ' . money_format($record->job->max_salary)}} per month</p>
                                            </div>
                                            <div class="date-posted">
                                                <p class="m-0">
                                                    Posted {{relative_time($record->job->created_at, 'hours ago')}}
                                                </p>
                                            </div>
                                            <div class="actions mt-4 d-flex gap-3 justify-content-start">
                                                <a href="{{route('interview-assessment', ['job_id' => $record->job->id, 'interview_id' => $record->interview[0]->job_interview_id])}}" class="btn btn-primary d-flex align-items-center gap-2">
                                                    <span>
                                                        Answer Interview
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane fade" id="v-pills-onboarding" role="tabpanel" aria-labelledby="v-pills-onboarding-tab" tabindex="0">
                    
                </div>
            </div>
        </div>
    </div>
 
    <div>
        @livewire('home.modals.update-profile', ['key' => 'update-profile-modal'])
    </div>
    <div>
        @livewire('home.modals.update-profile-resume', ['key' => 'update-profile-resume-modal'])
    </div>
    <div>
        @livewire('home.modals.update-profile-image', ['key' => 'update-profile-image-modal'])
    </div>
    <div>
        @livewire('home.modals.update-profile-skills', ['key' => 'update-profile-skills-modal'])
    </div>

</div>