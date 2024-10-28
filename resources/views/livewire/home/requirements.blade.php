<div>
    <div wire:ignore.self class="row d-flex justify-content-center mt-5">
        <div class="col-7">
            <form wire:submit.prevent='save'>
                <div class="jobs-lists">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card shadow px-2">
                                <div class="card-header border-0 bg-transparent">
                                    <div class="position-title">
                                        <h4 class="m-0 text-uppercase">{{$record->job->position}}</h4>
                                    </div>
                                    <div class="company-info">
                                        <p class="m-0 text-uppercase">{{$record->job->company_name}}</p>
                                        <p class="m-0 text-uppercase">{{$record->job->location}}</p>
                                    </div>
                                    <div class="salary">
                                        <p class="m-0 text-uppercase">{{money_format($record->job->min_salary) . ' - ' . money_format($record->job->max_salary)}} per month</p>
                                    </div>
                                    <div class="date-posted">
                                        <p class="m-0">
                                            Posted {{relative_time($record->job->created_at, 'hours ago')}}
                                        </p>
                                    </div>
                                </div>    
                                <div class="card-body py-4">
                                    <div class="d-flex gap-3 justify-content-start">
                                        <button type="button" wire:click="go_back" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">Go Back</button>
                                        <a href="{{route('home.view-job', ['slug' => $record->job->slug])}}" class="btn btn-outline-primary d-flex align-items-center gap-2 px-5 py-3 text-uppercase fw-bold">View Job</a>
                                    </div> 
                                </div>                           
                            </div>
                        </div>
                        @if (empty($responses))
                            <div class="col-12">
                                <div class="card-body pt-1 pb-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary text-uppercase px-4 py-3" wire:click="add_item">Add Requirement</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @foreach ($responses as $index => $response)
                            <div class="col-12 mb-4">
                                <div class="card shadow">
                                    <div class="card-body p-4">
                                        <div class="col-12 mb-3 d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger" wire:click="remove_item({{$index}})">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                        <div class="col-12 mb-3" wire:ignore>
                                            <select wire:model="responses.{{$index}}.type" id="responses.type" class="form-select">
                                                <option value=""> - CHOOSE - </option>
                                                @foreach ($requirements as $requirement)
                                                    <option value="{{$requirement->id}}">{{$requirement->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="error-field mt-3">
                                                @error('responses.' . $index . '.type') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <input type="file" wire:model="responses.{{$index}}.document" id="responses.document" class="w-100 form-control">
                                            @if (isset($previews[$index]))
                                                @if ($previews[$index]['type'] === 'image')
                                                    <img src="{{ $previews[$index]['url'] }}" class="img-fluid mt-3" alt="Preview Image" style="height: 500px; width: 100%; object-fit:cover;">
                                                @elseif ($previews[$index]['type'] === 'pdf')
                                                    <iframe src="{{ $previews[$index]['url'] }}" width="100%" height="500px" class="mt-3"></iframe>                                                    
                                                @endif
                                            @endif
                                            <div class="error-field mt-3">
                                                @error('responses.' . $index . '.document') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if (!empty($responses))
                            <div class="col-12">
                                <div class="card-body pt-1 pb-4">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-primary text-uppercase px-4 py-3" wire:click="add_item">Add Requirement</button>
                                        <button type="submit" class="btn btn-primary text-uppercase px-5 py-3" wire:click="save">Submit</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
