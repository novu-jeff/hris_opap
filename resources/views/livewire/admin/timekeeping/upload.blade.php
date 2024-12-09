<div>
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