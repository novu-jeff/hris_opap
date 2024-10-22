<div>
    {{-- Applicant Information Modal --}}
    <div class="modal fade" wire:ignore.self id="applicant_info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Applicant Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>ID:</strong> {{$applicant_information->id ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>First Name:</strong> {{$applicant_information->firstname ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Middle Name:</strong> {{$applicant_information->middlename ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Last Name:</strong> {{$applicant_information->lastname ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Phone Number:</strong> {{$applicant_information->phone_no ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Telephone Number:</strong> {{$applicant_information->tel_no ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Sex:</strong> {{$applicant_information->sex ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Birthday:</strong> {{$applicant_information->birthday ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Civil Status:</strong> {{$applicant_information->civil_status ?? null}}
                                </div>
                                <div class="col-md-12 mb-3 text-uppercase">
                                    <strong>Address:</strong> {{$applicant_information->address ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>Province:</strong> {{$applicant_information->province ?? null}}
                                </div>
                                <div class="col-md-6 mb-3 text-uppercase">
                                    <strong>City:</strong> {{$applicant_information->city ?? null}}
                                </div>
                                <div class="col-md-12 mb-3 text-uppercase">
                                    <strong>Resume:</strong> 
                                    @if ($applicant_information && !is_null($applicant_information->resume))
                                        <a target="_blank" href="{{ Storage::url('applicant/users/' . $applicant_information->id . '/' . $applicant_information->resume) }}" class="text-lowercase">
                                            {{ $applicant_information->resume }}
                                        </a>
                                    @else
                                        <span class="text-lowercase">No resume uploaded</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-uppercase  d-flex justify-content-end">
                            <div class="profile" style="width: 150px; height: 150px;">
                                <img src="{{
                                    isset($applicant_information) && !is_null($applicant_information->image)
                                        ? Storage::url('applicant/users/' . $applicant_information->id . '/' . $applicant_information->image)
                                        : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                                }}" class="w-100 h-100" style="width: 150px; height: 150px; object-fit:cover" alt="" srcset="">
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Choose Interview Modal --}}
    <div class="modal fade" wire:ignore.self id="select_interview" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Select Interview</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($interview))
                        <div class="row">
                            @foreach($interview as $key => $item)
                                <div class="col-12 col-md-6">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div>
                                            <input type="checkbox" wire:model='selected_interview.{{$item->id}}'  class="form-check">
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-uppercase">{{$item->name}}</h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">No Interviews created, please add first.</div>
                    @endif
                </div>
                @if (!empty($interview))
                    <div class="modal-footer d-flex justify-content-end">
                        <button class="btn btn-primary" wire:click="set_interview(false)">Proceed</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Applicant Responses Modal --}}
    <div class="modal fade" wire:ignore.self id="applicant_responses" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">View Responses</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!is_null($applicant_responses))
                        <div class="accordion" id="accordionExample">
                            @foreach ($applicant_responses as $index => $response)
                            <div class="accordion-item mb-3 shadow-sm border-1">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button text-uppercase {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                        {{$response->interview->name}}
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @foreach ($response->interview->items as $key => $item)
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; color: #fff; background-color: #225F8B">
                                                {{ $key + 1 }}
                                            </div>
                                            <div class="w-100">
                                                <div class="w-100 mb-2">
                                                    <input type="text" class="form-control" value="{{ $item->name }}" readonly>
                                                </div>
                                                <div class="w-100 mb-2">
                                                    <input type="text" class="form-control" value="" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Send Job Offer Modal--}}
    <div class="modal fade" wire:ignore.self id="applicant_job_offer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Send Job Offer</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="mb-2" for="subject">Subject <span class="text-danger">*</span></label>
                            <input type="text" wire:model="job_offer.subject" id="subject" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="mb-2" for="body">Body <span class="text-danger">*</span></label>
                            <div wire:ignore>
                                <textarea wire:model="job_offer.body" id="ckeditor" class="form-control text-uppercase"></textarea>
                            </div>
                            <div class="error-field">
                                @error('body') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="mb-2" for="attachment">Attachments <span class="text-danger">*</span></label>
                            <input type="file" wire:model="job_offer.attachment" id="attachment" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('attachment') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button class="btn btn-primary" wire:click="send_offer(true)">Proceed</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Requirements Checklist Modal--}}
    <div class="modal fade" wire:ignore.self id="applicant_requirements" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Requirements Checklist</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($requirements))
                        <div class="row">
                            @foreach($requirements as $key => $item)
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <input type="checkbox" wire:model='selected_requirements.{{$item->id}}'  class="form-check">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-uppercase">{{$item->name}}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">No Interviews created, please add first.</div>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button class="btn btn-primary" wire:click="set_checklist(true)">Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{route('job.applicants.index', ['status' => 'pending'])}}" class="nav-link {{$status == 'pending' ? 'active' : ''}}" id="pills-pending-tab"  aria-selected="true">Pending</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('job.applicants.index', ['status' => 'interview'])}}" class="nav-link {{$status == 'interview' ? 'active' : ''}}" id="pills-interview-tab" type="button" role="tab" aria-controls="pills-interview" aria-selected="false">Interview</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('job.applicants.index', ['status' => 'placement'])}}" class="nav-link {{$status == 'placement' ? 'active' : ''}}" id="pills-placement-tab" type="button" role="tab" aria-controls="pills-placement" aria-selected="false">Placement</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('job.applicants.index', ['status' => 'onboarding'])}}" class="nav-link {{$status == 'onboarding' ? 'active' : ''}}" id="pills-onboarding-tab"type="button" role="tab" aria-controls="pills-onboarding" aria-selected="false">Onboarding</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('job.applicants.index', ['status' => 'hired'])}}" class="nav-link {{$status == 'hired' ? 'active' : ''}}" id="pills-hired-tab"  type="button" role="tab" aria-controls="pills-hired" aria-selected="false">Hired</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{route('job.applicants.index', ['status' => 'rejected'])}}" class="nav-link {{$status == 'rejected' ? 'active' : ''}}" id="pills-rejected-tab"  type="button" role="tab" aria-controls="pills-rejected" aria-selected="false">Rejected</a>
                </li>
            </ul>
            <div class="mt-4" wire:ignore>
                <table class="table w-100">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Company</th>
                            <th>Position</th>
                            @if ($status == 'placement')
                                <th>
                                    Offers
                                </th>
                            @endif
                            <th>Date Applied</th>
                            <th style="max-width: 200px;">Action</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @foreach($records as $record)
                            <tr data-id="{{$record->id}}">
                                <td>{{$record->applicant_no}}</td>
                                <td>{{$record->job->company_name}}</td>
                                <td>{{$record->job->position}}</td>
                                @if ($status == 'placement')
                                    <td>
                                        {{$records[0]->offer ? 'Offer Sent' : 'No offer sent'}}
                                    </td>
                                @endif
                                <td>{{$record->created_at}}</td>
                                <td>
                                    <a target="_blank" href="{{route('home.view-job', ['slug' => $record->job->slug])}}" class="btn btn-info mx-1">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <button class="btn btn-success mx-1" wire:click="view_applicant({{$record->id}})">
                                        <i class="fa-solid fa-person"></i>
                                    </button>
                                    @if ($status == 'interview')
                                        <button wire:click="view_responses({{$record->id}})" class="btn btn-primary mx-1">
                                            <i class="fa-solid fa-reply"></i>
                                        </button>
                                    @endif
                                    @if($status != 'rejected' && $status != 'hired')
                                        <button wire:click="set_action('rejected', {{$record->id}})" class="btn btn-danger mx-1">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    @endif
                                    @if ($status === 'placement' && is_null($record->offer))
                                        <button wire:click="send_offer(false, {{$record->id}})" class="btn btn-primary mx-1">
                                            <i class="fa-regular fa-paper-plane"></i>
                                        </button>
                                    @endif
                                    @if ($status === 'onboarding')
                                        <button wire:click="set_checklist(false, {{$record->id}})" class="btn btn-primary mx-1">
                                            <i class="fa-solid fa-list-check"></i>
                                        </button>
                                    @endif
                                    @if (in_array($status, ['pending', 'interview', 'placement', 'onboarding']))
                                        <button wire:click="set_action('process', {{$record->id}})" class="btn btn-primary mx-1">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </button>   
                                    @else
                                    <button wire:click="set_action('delete', {{$record->id}})" class="btn btn-danger mx-1">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>           
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>