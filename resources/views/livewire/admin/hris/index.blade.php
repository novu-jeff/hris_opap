<div>
    <div class="modal fade" id="alert_employee" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Upload Reports</h1>
                    <button type="button" class="btn-close" wire:click="close_upload_employee" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($resultMessage)
                        <ul class="nav nav-pills d-flex justify-content-center" wire:ignore id="myTab" role="tablist">
                            <?php foreach ($resultMessage as $index => $message): ?>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-uppercase <?= $index === 0 ? 'active' : ''; ?>" id="tab-<?= $index ?>" data-bs-toggle="tab" href="#content-<?= $index ?>" role="tab"><?= $message['section'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <hr>
                        <div class="tab-content" id="myTabContent">
                            @foreach ($resultMessage as $index => $message)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="content-{{ $index }}" role="tabpanel">
                                    <p class="text-uppercase"><strong>Inserted Records: ({{ $message['insertedCount'] }})</strong></p>
                                    <ul class="text-uppercase">
                                        @if (!empty($message['insertedList']))
                                            @foreach ($message['insertedList'] as $inserted)
                                                <li>
                                                    {{ $inserted['message'] }}
                                                    <span class="ms-2">
                                                        <a target="_blank" href="{{route('hris.show', ['employee_no' => $inserted['employee_no']])}}" class="text-decoration-underline text-primary">View</a>
                                                    </span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li>No records inserted</li>
                                        @endif
                                    </ul>
                                    <p class="text-uppercase"><strong>Updated Records: ({{ $message['updatedCount'] }})</strong></p>
                                    <ul class="text-uppercase">
                                        @if (!empty($message['updatedList']))
                                            @foreach ($message['updatedList'] as $updated)
                                                <li>
                                                    {{ $updated['message'] }}
                                                    <span class="ms-2">
                                                        <a target="_blank" href="{{route('hris.show', ['employee_no' => $updated['employee_no']])}}" class="text-decoration-underline text-primary">View</a>
                                                    </span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li>No records updated</li>
                                        @endif
                                    </ul>                                    
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="upload_employee" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Add Employee</h1>
                    <button type="button" class="btn-close" wire:click="close_upload_employee" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button  wire:ignore.self class="nav-link active" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload-add" type="button" role="tab" aria-controls="upload" aria-selected="false">
                                File Upload
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{route('hris.manual')}}" class="nav-link" id="manual-tab" role="tab" aria-controls="manual" aria-selected="true">
                                Manual Adding
                            </a>
                        </li>
                    </ul>
                
                    <hr>
                    <!-- Pills Content -->
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Upload Tab -->
                        <div class="tab-pane fade show active" wire:ignore.self id="upload-add" role="tabpanel" aria-labelledby="upload-tab">
                            <label class="mb-2" for="file">File Upload</label>
                            <input type="file" wire:model="file" id="file" class="form-control">
                            <div class="mt-2 text-muted fw-bold text-uppercase d-flex justify-content-between align-items-center" style="font-size: 13px">
                                <small>Note: only files xlsx or xls are allowed.</small>
                                <small><a href="{{asset('templates/HRIS EMPLOYEE TEMPLATE.xlsx')}}" class="nav-link text-decoration-underline">Download Template</a></small>
                            </div>
                            <div wire:loading wire:target="file" class="mt-2 text-center text-muted">
                                <p>Please Wait... <i class="fa-solid fa-spinner fa-spin"></i></p>
                            </div>
                            @error('file') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                            <div class="mt-3">
                                @if($upload_preview)
                                    File Ready to import: <a href="{{$upload_preview}}">{{$upload_preview}}</a>
                                @endif
                            </div>
                            <div class="mt-4 d-flex justify-content-end">
                                @if($upload_preview)
                                    <button class="btn btn-primary px-5 py-3 text-uppercase fw-bold" 
                                            wire:click="upload_file"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove>Upload File</span>
                                        <span wire:loading>Importing <i class="fa-solid fa-spinner fa-spin"></i></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mb-5 gap-3">
        <button class="btn btn-primary px-5 py-3 text-uppercase" data-bs-toggle="modal" data-bs-target="#upload_employee">Add Employee</button>
    </div>

    <div>
        <div class="table-responsive">
            <table class="table table-striped w-100">
                <thead>
                    <tr>
                        <th></th>
                        <th>Employee No</th>
                        <th>Employee Name</th>
                        <th>Date Hired</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($lazy)
                        <!-- Skeleton Rows -->
                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                <td class="text-center">
                                    <div class="skeleton skeleton-circle" style="width: 50px; height: 50px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text" style="width: 80px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text" style="width: 120px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text" style="width: 100px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-button" style="width: 60px; height: 30px;"></div>
                                </td>
                            </tr>
                        @endfor
                    @else
                        @if (!empty($employees))
                            <!-- Loaded Employee Rows -->
                            @foreach($employees as $key => $item)
                                <tr data-id="{{$item->employee_no}}">
                                    <td class="text-center">
                                        <img style="width: 50px; height: 50px;" src="{{
                                            $item->personal && $item->personal->profile 
                                                ? Storage::url('employee/users/'.$item->personal->employee_id.'/'.$item->personal->profile) 
                                                : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10'
                                        }}">
                                    </td>
                                    <td>{{$item->employee_no}}</td>
                                    <td>{{$item->personal->firstname . ' ' . $item->personal->lastname}}</td>
                                    <td>{{format_date($item->date_hired, 'day_date_string')}}</td>
                                    <td wire:ignore.self>
                                        <a target="_blank" href="{{route('hris.show', ['employee_no' => $item->employee_no])}}" class="btn btn-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <button id="remove" data-id="{{$item->employee_no}}" class="btn btn-danger mx-1">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Skeleton CSS -->
        <style>
            .skeleton {
                background-color: #e0e0e0;
                border-radius: 4px;
                animation: pulse 1.5s infinite ease-in-out;
            }
            .skeleton-circle {
                border-radius: 50%;
            }
            .skeleton-text {
                height: 16px;
                margin-top: 8px;
            }
            .skeleton-button {
                border-radius: 6px;
            }
            @keyframes pulse {
                0% {
                    background-color: #e0e0e0;
                }
                50% {
                    background-color: #f0f0f0;
                }
                100% {
                    background-color: #e0e0e0;
                }
            }
        </style>        
    </div>
</div>

@section('script')
    <script>
        $(function() {
            setTimeout(() => {
                Livewire.dispatch('loading');
            }, 1000);

            $(document).on('click', '#remove', function() {
                const id = $(this).data('id');
                Livewire.dispatch('remove', [true, id]);
            });

        })
    </script>
@endsection