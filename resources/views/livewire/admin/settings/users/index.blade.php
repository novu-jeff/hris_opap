<div>

    <div class="modal fade" wire:ignore.self id="user_info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">About User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 ">
                    @if ($type === 'applicants')
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                            <button class="nav-link text-uppercase fw-medium active" id="pills-info-tab" data-bs-toggle="pill" data-bs-target="#pills-info" type="button" role="tab" aria-controls="pills-info" aria-selected="true">Personal Information</button>
                            </li>
                            <li class="nav-item" role="presentation">
                            <button class="nav-link text-uppercase fw-medium" id="pills-applied-tab" data-bs-toggle="pill" data-bs-target="#pills-applied" type="button" role="tab" aria-controls="pills-applied" aria-selected="false">Jobs Applied</button>
                            </li>
                        </ul>
                        <div class="tab-content mt-4" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-info" role="tabpanel" aria-labelledby="pills-info-tab" tabindex="0">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>ID:</strong> {{$user_information->id ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>First Name:</strong> {{$user_information->firstname ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Middle Name:</strong> {{$user_information->middlename ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Last Name:</strong> {{$user_information->lastname ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Phone No:</strong> {{$user_information->phone_no ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Tel No:</strong> {{$user_information->tel_no ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Sex:</strong> {{$user_information->sex ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Birthday:</strong> {{$user_information->birthday ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Civil Status:</strong> {{$user_information->civil_status ?? null}}
                                            </div>
                                            <div class="col-12 mb-3">
                                                <hr>
                                            </div>
                                            <div class="col-md-12 mb-3 text-uppercase">
                                                <strong>Address:</strong> {{$user_information->address ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>Province:</strong> {{$user_information->province ?? null}}
                                            </div>
                                            <div class="col-md-6 mb-3 text-uppercase">
                                                <strong>City:</strong> {{$user_information->city ?? null}}
                                            </div>
                                            <div class="col-12 mb-3">
                                                <hr>
                                            </div>
                                            <div class="col-md-12 mb-3 text-uppercase">
                                                <strong>Resume:</strong> 
                                                @if ($user_information && !is_null($user_information->resume))
                                                    <a target="_blank" href="{{ Storage::url('applicant/users/' . $user_information->id . '/' . $user_information->resume) }}" class="text-lowercase">
                                                        {{ $user_information->resume }}
                                                    </a>
                                                @else
                                                    <span class="text-lowercase">No resume uploaded</span>
                                                @endif
                                            </div>
                                            <div class="col-md-12 mb-3 text-uppercase">
                                                @if (!is_null($user_information) && !$user_information->skills->isEmpty())
                                                    <strong>Skills:</strong> 
                                                @endif
                                                <div class="skill-content pt-2 pb-3">
                                                    @if (!is_null($user_information) && !$user_information->skills->isEmpty())
                                                        @foreach ($user_information->skills as $skill)
                                                            <div class="skill-box">
                                                                {{$skill->skills->name}}
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            @if (!empty($application_information->level))
                                                <div class="col-12 mb-3">
                                                    <hr>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3 text-uppercase  d-flex justify-content-end">
                                        <div class="profile" style="width: 150px; height: 150px;">
                                            <img 
                                                src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode(($user_information->firstname ?? '') . ' ' . ($user_information->lastname ?? '')) }}" 
                                                class="w-100 h-100" 
                                                style="width: 150px; height: 150px; object-fit:cover" 
                                                alt="User Avatar"
                                            >
                                        </div>                            
                                    </div>
                                    @if (!empty($user_information->level))
                                        <div class="col-md-12 mb-3 text-uppercase">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <strong>Highest School Attaintment:</strong> {{$user_information->level ?? null}}
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <strong>School Name:</strong> {{$user_information->school_name ?? null}}
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <strong>Course:</strong> {{$user_information->course ?? null}}
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <strong>From:</strong> {{$user_information->started ?? null}}
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <strong>To:</strong> {{$user_information->finished ?? null}}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-applied" role="tabpanel" aria-labelledby="pills-applied-tab" tabindex="0">
                                @if (isset($user_information) && !is_null($user_information->applied))
                                    <div class="jobs-lists">
                                        <div class="row">
                                            @forelse ($user_information->applied as $record)
                                                <div class="col-12 mb-4 px-4">
                                                    <div class="card shadow px-2" style="border-left: 8px solid #225F8B; cursor: pointer">
                                                        <div class="card-body py-4">
                                                            <div class="w-100 d-flex justify-content-between align-items-start gap-5">
                                                                <div class="w-100">
                                                                    <div class="date-posted float-end">
                                                                        <p class="m-0">
                                                                            Applied {{relative_time($record->created_at, 'hours ago')}}
                                                                        </p>
                                                                    </div>
                                                                    <div class="info">
                                                                        <div class="position-title">
                                                                            <h4 class="m-0 text-uppercase">{{$record->job->position}}</h4>
                                                                        </div>
                                                                        <div class="company-info">
                                                                            <p class="m-0 text-uppercase">{{$record->job->company_name}}</p>
                                                                            <p class="m-0 text-uppercase">{{$record->job->location . ' • ' . str_replace('-', ' ', $record->job->setup . ' • ' . str_replace('-', ' ', $record->job->type))}}</p>
                                                                            <p class="m-0 text-uppercase">{{money_format($record->job->min_salary) . ' - ' . money_format($record->job->max_salary)}}</p>
                                                                        </div>
                                                                        <hr>
                                                                    </div>
                                                                    <div class="status">
                                                                        <p class="m-0">
                                                                            {!!application_status($record->status)!!} 
                                                                        </p>
                                                                    </div>
                                                                    <div class="d-flex justify-content-end">
                                                                        <a href="{{route('home.view-job', ['slug' => $record->job->slug])}}" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">View Job</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    <div class="alert alert-info text-center text-uppercase">No Jobs Applied Yet</div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @else
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 mt-4">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="{{route('users.index', ['type' => 'applicants'])}}" class="nav-link {{$type == 'applicants' ? 'active' : ''}}" id="pills-applicants-tab"  aria-selected="true">Applicants</a>
                </li>
                <li class="nav-item text-uppercase fw-bold" role="presentation">
                    <a href="{{route('users.index', ['type' => 'employees'])}}" class="nav-link {{$type == 'employees' ? 'active' : ''}}" id="pills-employees-tab" type="button" role="tab" aria-controls="pills-employees" aria-selected="false">Employees</a>
                </li>
                @can('read roles')
                    <li class="nav-item text-uppercase fw-bold" role="presentation">
                        <a href="{{route('users.index', ['type' => 'admins'])}}" class="nav-link {{$type == 'admins' ? 'active' : ''}}" id="pills-admin-tab" type="button" role="tab" aria-controls="pills-admin" aria-selected="false">Administrators</a>
                    </li>
                @endcan
            </ul>
            <div class="actions d-flex justify-content-end mb-5">
                @if($type == 'employees')
                    <a href="{{route('hris.manual')}}"  class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
                @elseif($type == 'admins')
                    <a href="{{route('admin.new')}}"  class="btn btn-primary text-uppercase px-5 py-3 fw-medium">Add New</a>
                @endif
            </div>
            <div class="mt-4">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center gap-2">
                        <label for="entries" class="form-label mb-0">Show entries:</label>
                        <select id="entries" wire:model.live="entries" class="form-select w-auto">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                            <option value="60">60</option>
                            <option value="70">70</option>
                            <option value="80">80</option>
                            <option value="90">90</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                        <label for="search" class="form-label mb-0">Search:</label>
                        <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Email</th>
                                @if($type === 'applicants')
                                    <th>Date Joined</th>
                                @elseif($type == 'employees')
                                    <th>Date Hired</th>
                                @endif
                                <th style="max-width: 200px;">Action</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @if($type === 'applicants')
                                @forelse($records as $record)
                                    <tr data-id="{{$record->id}}">
                                        <td class="text-center">
                                            <img style="width: 50px; height: 50px;"
                                                src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($record->firstname . ' ' . $record->lastname) }}">              
                                        </td>
                                        <td>{{$record->firstname . ' ' . $record->lastname}}</td>
                                        <td>{{$record->email}}</td>
                                        @if($type === 'applicants')
                                            <td>{{format_date($record->created_at, 'date_string')}}</td>
                                        @endif
                                        <td>
                                            <button class="btn btn-success mx-1" wire:click="view_user({{$record->id}})">
                                                <i class="fa-solid fa-person"></i>
                                            </button>
                                            <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>  
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                                    </tr>
                                @endforelse
                                @elseif($type === 'employees')
                                @forelse($records as $record)
                                    <tr data-id="{{$record->id}}">
                                        <td class="text-center">
                                            <img style="width: 50px; height: 50px;"
                                                src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($record->personal->firstname . ' ' . $record->personal->lastname) }}">              
                                        </td>
                                        <td>{{$record->personal->firstname . ' ' . $record->personal->lastname}}</td>
                                        <td>{{$record->account->email}}</td>
                                        @if($type === 'employees')
                                            <td>{{$record->date_hired}}</td>
                                        @endif
                                        <td>
                                            <a target="_blank" href="{{route('hris.show', ['employee_no' => $record->employee_no, 'form' => 'information'])}}" class="btn btn-primary mx-1">
                                                <i class="fa-solid fa-person"></i>
                                            </a>
                                            <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>  
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                                    </tr>
                                @endforelse
                                @elseif($type === 'admins')
                                @forelse($records as $record)
                                    <tr data-id="{{$record->id}}">
                                        <td class="text-center">
                                            <img style="width: 50px; height: 50px;"
                                                src="https://ui-avatars.com/api/?background=005668&color=ffffff&font-size=0.4&bold=true&name={{ urlencode($record->name) }}">              
                                        </td>
                                        <td>{{$record->name}}</td>
                                        <td>{{$record->email}}</td>
                                        @if($type === 'employees')
                                            <td>{{$record->date_hired}}</td>
                                        @endif
                                        <td>
                                            <a href="{{route('admin.update', ['id' => $record->id])}}" class="btn btn-primary mx-1">
                                                <i class="fa-solid fa-edit"></i>
                                            </a> 
                                            <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>  
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                                    </tr>
                                @endforelse
                            @else
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $records->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
    </div>
</div>
