<form wire:submit.prevent="save" wire:target="save">
    <div class="row">
        <div class="col-12">
            <div class="card shadow p-4">

                {{-- HEADER --}}
                <div class="card-header bg-transparent border-0">
                    <p class="text-muted mb-0 text-uppercase fst-italic">
                        All <span class="text-danger">*</span> is required
                    </p>
                </div>

                <hr class="mx-3">

                {{-- BODY --}}
                <div class="card-body">
                    <div class="row mb-3">

                        {{-- DATE --}}
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input
                                type="date"
                                class="form-control @error('fields.date') is-invalid @enderror"
                                wire:model="fields.date"
                                @if(($fields['status'] ?? null) === 'disapproved') readonly @endif
                            >
                            @error('fields.date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- START TIME --}}
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="timepicker form-control @error('fields.start_time') is-invalid @enderror"
                                wire:model="fields.start_time"
                                @if(($fields['status'] ?? null) === 'disapproved') readonly @endif
                            >
                            @error('fields.start_time')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- END TIME --}}
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="timepicker form-control @error('fields.end_time') is-invalid @enderror"
                                wire:model="fields.end_time"
                                @if(($fields['status'] ?? null) === 'disapproved') readonly @endif
                            >
                            @error('fields.end_time')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- JUSTIFICATION --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Justification <span class="text-danger">*</span></label>

                            <textarea
                                rows="5"
                                class="form-control @error('fields.justification') is-invalid @enderror
                                    @if(($fields['status'] ?? null) === 'disapproved') restricted @endif"
                                wire:model="fields.justification"
                                @if(($fields['status'] ?? null) === 'disapproved') readonly @endif
                            ></textarea>

                            @error('fields.justification')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- DISAPPROVAL NOTE --}}
                        @if(($fields['status'] ?? null) === 'disapproved')
                            <div class="col-12 mb-4">
                                <label class="mb-2">Reason for Disapproval</label>
                                <textarea
                                    rows="3"
                                    class="form-control restricted"
                                    readonly
                                >{{ $fields['disapproval_note'] ?? 'N/A' }}</textarea>
                            </div>
                        @endif

                    </div>
                </div>

                <hr class="mx-3">

                {{-- FOOTER --}}
                @if(($fields['status'] ?? null) !== 'disapproved')
                    <div class="card-footer bg-transparent border-0 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-3 fw-bold">
                            <span wire:loading.remove wire:target="save">
                                Proceed <i class="fa-solid fa-arrow-right ms-2"></i>
                            </span>
                            <span wire:loading wire:target="save">
                                Proceeding <i class="fa-solid fa-spinner fa-spin ms-2"></i>
                            </span>
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>
</form>

{{-- SCRIPTS --}}
@push('scripts')
<script>
    function initTimePicker() {
        if ($('.timepicker').length) {
            $('.timepicker').timepicker({
                timeFormat: 'H:i'
            });
        }
    }

    document.addEventListener('livewire:navigated', initTimePicker);

    Livewire.hook('message.processed', () => {
        initTimePicker();
    });
</script>
@endpush
