<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            {{ $loan_id ? 'Edit Loan Application' : 'New Loan Application' }}
        </h5>
    </div>

    <div class="card-body">

        {{-- Loan Application Form --}}
        <form wire:submit.prevent="save">

            <!-- Loan Type -->
            <div class="mb-3">
                <label class="form-label">Loan Type</label>
                <select class="form-select" wire:model.defer="loan_type_id">
                    <option value="">-- Select Loan Type --</option>
                    @foreach($loanTypes as $type)
                        <option value="{{ $type->id }}" 
                            {{ $loan_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('loan_type_id') 
                    <small class="text-danger">{{ $message }}</small> 
                @enderror
            </div>

            <!-- Loan Amount -->
            <div class="mb-3">
                <label class="form-label">Loan Amount</label>
                <input type="number" class="form-control" wire:model.defer="principal_amount" 
                       value="{{ old('principal_amount', $principal_amount) }}">
                @error('principal_amount') 
                    <small class="text-danger">{{ $message }}</small> 
                @enderror
            </div>

            <!-- Term -->
            <div class="mb-3">
                <label class="form-label">Term (Months)</label>
                <input type="number" class="form-control" wire:model.defer="term_months" 
                       value="{{ old('term_months', $term_months) }}">
                @error('term_months') 
                    <small class="text-danger">{{ $message }}</small> 
                @enderror
            </div>

            <button class="btn btn-primary">
                {{ $loan_id ? 'Update Loan' : 'Submit Loan' }}
            </button>

        </form>

    </div>
</div>
