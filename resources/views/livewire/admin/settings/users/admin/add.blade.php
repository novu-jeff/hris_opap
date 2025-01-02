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
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="name" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="email">Email <span class="text-danger">*</span></label>
                            <input type="text" wire:model="email" id="email" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" wire:model="username" id="username" class="form-control text-uppercase">
                            <div class="error-field">
                                @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="roles">Role <span class="text-danger">*</span></label>
                            <select wire:model="role" id="role" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                @foreach($roles as $role) 
                                    <option value="{{$role->name}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="password" id="password" class="form-control text-uppercase" placeholder="⦁⦁⦁⦁⦁⦁⦁⦁">
                            <div class="error-field">
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" wire:model="confirm_password" id="confirm_password" class="form-control text-uppercase" placeholder="⦁⦁⦁⦁⦁⦁⦁⦁">
                            <div class="error-field">
                                @error('confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
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
