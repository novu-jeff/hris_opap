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
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="type">Type <span class="text-danger">*</span></label>
                            <select wire:model="type" id="type" class="form-select">
                                <option value=""> - CHOOSE - </option>
                                <option value="casual">Casual Leave</option>
                                <option value="medical">Medical Leave</option>
                                <option value="emergency">Emergency Leave</option>
                                <option value="sick">Sick Leave</option>
                                <option value="unpaid">Unpaid Leave</option>
                            </select>
                            <div class="error-field">
                                @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="from">From <span class="text-danger">*</span></label>
                            <input type="date" wire:model="from" id="from" class="form-control">
                            <div class="error-field">
                                @error('from') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="to">To <span class="text-danger">*</span></label>
                            <input type="date" wire:model="to" id="to" class="form-control">
                            <div class="error-field">
                                @error('to') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="reason">Reason <span class="text-danger">*</span></label>
                            <textarea wire:model="reason" id="reason" cols="30" rows="5" class="form-control" placeholder="Write something..."></textarea>
                            <div class="error-field">
                                @error('reason') <span class="text-danger">{{ $message }}</span> @enderror
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
