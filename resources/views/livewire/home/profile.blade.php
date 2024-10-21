<div class="profile">

    <div class="row">
        <div class="col-12 col-md-4 col-lg-3 mb-4">
            <div class="sticky">
                <div class="profile-section">
                    <div class="d-flex justify-content-center">
                        <div class="img-content">
                            <img src="{{
                                $record->image ? Storage::url('applicant/users/'.$record->user_id.'/'.$record->image) : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                            }}" alt="">
                            <div class="edit-icon" data-bs-toggle="modal" data-bs-target="#update-profile-image-modal">
                                <i class="fa-solid fa-camera"></i>
                            </div>
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
                    <h5>Skills</h5>
                    <div class="skill-content">
                        <div class="skill-box">
                            HTML
                        </div>
                        <div class="skill-box">
                            CSS 3
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8 col-lg-9 mb-4">
            <div class="tab-content" id="v-pills-tabContent">
                <div wire:ignore.self class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab" tabindex="0">
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
                                    <li>Highest Attainment: <span class="text-uppercase">{{!is_null($record->level) ? $record->level : 'No info'}}</span></li>
                                    <li>School Name: <span class="text-uppercase">{{!is_null($record->school_name) ? $record->school_name : 'No info'}}</span></li>
                                    <li>Course: <span class="text-uppercase">{{!is_null($record->course) ? $record->course : 'No info'}}</span></li>
                                    <li>School Year: <span class="text-uppercase">{{!is_null($record->started) && !is_null($record->finished) ? format_date($record->started, 'date_string') . ' - ' . format_date($record->finished, 'date_string') : 'No info'}}</span></li>
                                </ul>
                            </div> 
                            <div class="mt-4" wire:ignore>
                                <h6>Resume</h6>
                                <p>Update your resume to streamline your job application and increase visibility to potential employers.</p>
                                <div data-bs-toggle="modal" data-bs-target="#update-profile-resume-modal">
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
                                </div>
                                {{-- <p>Current Resume: <a target="_blank" href="{{Storage::url('applicant/users/'.$record->user_id.'/'.$record->resume)}}">{{$record->resume}}</a></p> --}}
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
                <div wire:ignore.self class="tab-pane fade" id="v-pills-interview" role="tabpanel" aria-labelledby="v-pills-interview-tab" tabindex="0">
                    <div class="card px-3 pt-4 pb-5 profile-content">
                        <div class="card-header bg-transparent border-0">
                            <h4>My Interviews</h4>
                            <p>
                                Here are the interview questions you need to answer.
                            </p>
                        </div>
                        <hr>
                        <div class="card-body">
                            
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane fade" id="v-pills-onboarding" role="tabpanel" aria-labelledby="v-pills-onboarding-tab" tabindex="0">
                    
                </div>
            </div>
        </div>
    </div>
 
    @livewire('home.modals.update-profile')

    
    {{-- <div >

        @livewire('home.modals.update-profile-image', [
            'record' => $record
        ], key('update-profile-image' . $record->user_id))

        @livewire('home.modals.update-profile-resume', [
            'record' => $record
        ], key('update-profile-resume' . $record->user_id))
    </div> --}}
</div>