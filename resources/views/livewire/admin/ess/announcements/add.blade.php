<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="banner">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" wire:model="banner" id="banner" class="form-control">
                            @if (isset($preview_banner))
                                <iframe src="{{ $preview_banner}}" width="100%" height="500px" class="mt-3"></iframe>                                                    
                            @endif
                            <div class="error-field">
                                @error('banner') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" wire:model="title" id="title" class="form-control">
                            <div class="error-field">
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="mb-2" for="content">Content <span class="text-danger">*</span></label>
                            <div wire:ignore>
                                <textarea wire:model="content" id="ckeditor" class="form-control text-uppercase"></textarea>
                            </div>
                            <div class="error-field">
                                @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="mb-2" for="attachments">Attachments</label>
                            <div class="d-flex justify-content-end">
                                <div wire:click="addRecord" class="btn btn-dark">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            </div>
                                <div class="w-100">
                                    @foreach($attachments as $key => $attachment)
                                        <div class="d-lg-flex align-items-start gap-3 w-100 mb-3">
                                            <div class="mb-3 w-50">
                                                <label class="mb-2" for="custom_file_{{ $key }}">Custom File Name</label>
                                                <input type="text" wire:model="attachments.{{ $key }}.name" id="custom_file_{{ $key }}" class="form-control">    
                                                <div class="error-field">
                                                    @error("attachments.{$key}.name") 
                                                        <span class="text-danger">{{ $message }}</span> 
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="mb-3 w-100">
                                                <label class="mb-2" for="file_{{ $key }}">File <span class="text-danger">*</span></label>
                                                <input type="file" wire:model="attachments.{{ $key }}.file" id="file_{{ $key }}" class="form-control">    
                                                <div class="error-field">
                                                    @error("attachments.{$key}.file") 
                                                        <span class="text-danger">{{ $message }}</span> 
                                                    @enderror
                                                </div>
                                            </div>
                                            <div style="margin-top: 28px">
                                                <div wire:click="removeRecord({{$key}})" class="btn btn-danger">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>  
                            </div>                            
                        </div>
                    </div>
                </div>
                <hr class="mx-3">
                <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                        <span wire:loading.remove wire:target="save">Save <i class="fa-solid fa-arrow-right ms-2"></i></span>
                        <span wire:loading wire:target="save">Saving <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
