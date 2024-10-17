<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-12 col-md-6">
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
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="level">Level <span class="text-danger">*</span></label>
                            <select wire:model="level" id="level" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                            <div class="error-field">
                                @error('level') <span class="text-danger">{{ $message }}</span> @enderror
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
        <div class="col-12 col-md-6">
            <div class="card shadow p-4">
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">All <span class="text-danger">*</span> is required</p>
                </div>
                <hr class="mx-3">
                <div class="card-body">
                    @if ($item_count > 0)
                        @for ($i = 0; $i < $item_count; $i++)
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Counter -->
                                    <div class="count d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; background-color: #225F8B">
                                        {{$i + 1}}
                                    </div>
                    
                                    <!-- Input Field -->
                                    <div class="question w-100">
                                        <input type="text" wire:model="items.{{$i}}" id="items" class="form-control">
                                    </div>
                    
                                    <!-- Conditionally display Remove button if item count is greater than 1 -->
                                    @if($item_count > 1)
                                        <div class="remove">
                                            <button type="button" class="btn btn-danger" wire:click="remove_item({{ $i }})">Remove</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="error-field mt-1 ms-5">
                                    @error('items.' . $i) <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endfor
                
                        <!-- Add Item Button -->
                        <div class="actions mt-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" wire:click="add_item">Add Item</button>
                        </div>
                    @endif
                </div>                
            </div>
        </div>
    </div>
</form>
