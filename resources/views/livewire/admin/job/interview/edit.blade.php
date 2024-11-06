<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12 col-md-5">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="mb-2" for="description">Description <span class="text-danger">*</span></label>
                            <div wire:ignore>
                                <textarea wire:model="description" id="ckeditor" class="form-control text-uppercase"></textarea>
                            </div>
                            <div class="error-field">
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Proceed</button>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-7">
            @if (empty($interview))
                <div class="actions d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-primary text-uppercase" wire:click="add_item">Add Item</button>
                </div>
            @endif
            @if (!empty($interview))
                <div class="accordion" id="accordionInterview">
                    @foreach ($interview as $interviewIndex => $item)
                        <div class="card shadow mb-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger" wire:click="remove_item({{$interviewIndex}})"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="count d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; background-color: #225F8B">
                                            {{$interviewIndex + 1}}
                                        </div>
                                        <div class="question w-100">
                                            <h6 class="mb-0">{{$item['question']}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @if ($item['type'] == 'simple')
                                        <div class="col-12 mb-3">
                                            <input type="text" class="form-control restricted text-uppercase" placeholder="Interviewee's Answer" readonly>
                                        </div>
                                    @elseif ($item['type'] == 'explanatory')
                                        <div class="col-12 mb-3">
                                            <textarea class="form-control restricted text-uppercase" rows="5" placeholder="Interviewee's Answer" readonly></textarea>
                                        </div>
                                    @elseif ($item['type'] == 'checkbox')
                                        <div class="col-12 mb-3">
                                            @foreach ($item['options'] as $optionIndex => $option)
                                                <div class="ms-5 form-check d-flex align-items-center gap-3">
                                                    <input type="checkbox" class="form-check-input" style="width: 1.5em; height: 1.5em" disabled>
                                                    <p class="mt-1 mb-0" for="checkbox-{{ $interviewIndex }}-{{ $optionIndex }}">
                                                        {{ $option }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($item['type'] == 'radio')
                                        <div class="col-12 mb-3">
                                            @foreach ($item['options'] as $optionIndex => $option)
                                                <div class="ms-5 form-check d-flex align-items-center gap-3">
                                                    <input type="radio" class="form-check-input" style="width: 1.5em; height: 1.5em" disabled>
                                                    <p class="mt-1 mb-0" for="radio-{{ $interviewIndex }}-{{ $optionIndex }}">
                                                        {{ $option }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($item['type'] == 'file')
                                        <div class="col-12 mb-3">
                                            <input type="file" name="file_upload" class="form-control">
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                    @if (!empty($interview))
                        <div class="actions d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary text-uppercase" wire:click="add_item">Add Item</button>
                        </div>
                    @endif
                </div>   
            @else
                <div class="alert alert-info text-uppercase text-center">No Interview Questions</div>
                <div class="error-field">
                    @error('interview') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            @endif           
        </div>
    </div>

    <div>
        <div class="modal fade" wire:ignore.self  id="add-item" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add-item-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 text-uppercase fw-bold" id="add-item-modalLabel">Add Item</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="mb-2">Interview Question <span class="text-danger">*</span></label>
                                <textarea wire:model="question" id="question" cols="30" rows="5" class="form-control" placeholder="Type something..."></textarea>
                                <div class="error-field">
                                    @error('question') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="mb-2">Interviewee Response Type <span class="text-danger">*</span></label>
                                <select wire:change="setItemType($event.target.value)" wire:model="type" name="type" id="response" class="form-select">
                                    <option value="simple">Simple</option>
                                    <option value="explanatory">Explanatory</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio</option>
                                    <option value="file">File Upload</option>
                                </select>      
                                <div class="error-field">
                                    @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>                     
                            </div>
                            <div class="col-12 mb-3">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            @if ($type == 'simple')
                                <div class="col-12 mb-3">
                                    <input type="text" class="form-control restricted text-uppercase" placeholder="Interviewee's Answer" readonly>
                                </div>
                            @elseif ($type == 'explanatory')
                                <div class="col-12 mb-3">
                                    <textarea class="form-control restricted text-uppercase" rows="5" placeholder="Interviewee's Answer" readonly></textarea>
                                </div>
                            @elseif ($type == 'checkbox')
                                <div class="col-12 mb-3">
                                    <div class="col-12 mb-3">
                                        @foreach ($options as $index => $choice)
                                            <div class="input-group mb-2">
                                                <input type="text" wire:model="options.{{ $index }}" class="form-control" placeholder="Option {{ $index + 1 }}">
                                                <button type="button" class="btn btn-danger" wire:click="remove_option({{ $index }})">Remove</button>
                                            </div>
                                            <div class="error-field">
                                                @error('options.'.$index) <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>  
                                        @endforeach
                                        <button type="button" class="btn btn-primary float-end" wire:click="add_option">Add Option</button>
                                        <div class="error-field">
                                            @error('options') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>  
                                    </div>
                                </div>
                            @elseif ($type == 'radio')
                                <div class="col-12 mb-3">
                                    @foreach ($options as $index => $choice)
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="options.{{ $index }}" class="form-control" placeholder="Option {{ $index + 1 }}">
                                            <button type="button" class="btn btn-danger" wire:click="remove_option({{ $index }})">Remove</button>
                                        </div>
                                        <div class="error-field">
                                            @error('options.'.$index) <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>  
                                    @endforeach
                                    <div class="error-field">
                                        @error('options') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>  
                                    <button type="button" class="btn btn-primary float-end" wire:click="add_option">Add Option</button>
                                </div>
                            @elseif ($type == 'file')
                                <div class="col-12 mb-3">
                                    <input type="file" name="file_upload" class="form-control" disabled>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" wire:click='save_item'>Proceed</button>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</form>

