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
                        <!-- Name Field -->
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="name" class="form-control">
                            <div class="error-field">
                                @error('name') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                
                        <!-- Description Field -->
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="description">Description <span class="text-danger">*</span></label>
                            <div wire:ignore>
                                <textarea wire:model="description" id="ckeditor" class="form-control text-uppercase" rows="5"></textarea>
                            </div>
                            <div class="error-field">
                                @error('description') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>                
                        <div class="col-12 mb-4">
                            <label class="mb-2">Work Days <span class="text-danger">*</span></label>
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    <div class="form-check mr-3">
                                        <input type="checkbox" wire:change="onSelect('{{ $day }}')" wire:model="{{ $day }}" class="form-check-input" id="{{ $day }}">
                                        <label class="form-check-label" for="{{ $day }}">{{ ucfirst($day) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="error-field">
                                @error('work_days') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>      
                        <div class="col-12 mb-4">
                            <div class="row d-flex flex-wrap">
                                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                    @if (!${$day})
                                        <div class="col-3 mb-3">
                                            <label for="{{ $day }}_remarks">{{ ucfirst($day) }} Remarks:</label>
                                            <input type="text" wire:model="{{ $day }}_remarks" class="form-control" id="{{ $day }}_remarks" placeholder="Why {{ ucfirst($day) }} is unchecked?">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="error-field">
                                @error("{$day}_remarks") 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
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
    </div>
</form>
