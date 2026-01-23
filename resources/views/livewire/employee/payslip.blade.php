<div>
    <div class="mt-5">

        @if($error)
            <div class="alert alert-danger text-center text-uppercase fw-bold my-4">{{$error}}</div>
        @else
            <div class="controls py-3 d-flex justify-content-between gap-3 align-items-center">
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center">
                        <button 
                            class="btn btn-sm btn-outline-primary" 
                            wire:click="changePeriod('control', '-1')"
                            wire:loading.attr="disabled" 
                            wire:loading.class="btn-secondary"
                            {{ $hasPrevious ? '' : 'disabled' }}
                            title="Monthly Payslip"
                            style="{{ $hasPrevious ? '' : 'opacity:0.5; cursor:not-allowed;' }}">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        
                        <div class="mx-3" id="cuttOffPeriod">
                             {{ \Carbon\Carbon::parse($currentPeriod->payroll_date)->format('F Y') }}
                        </div>
                                                
                        <button 
                        class="btn btn-sm btn-outline-primary" 
                        wire:click="changePeriod('control', '1')"
                        wire:loading.attr="disabled" 
                        wire:loading.class="btn-secondary"
                        {{ $hasNext ? '' : 'disabled' }}
                        title="Monthly Payslip"
                        style="{{ $hasNext ? '' : 'opacity:0.5; cursor:not-allowed;' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>                
                    </div>
                </div>
                @if($requestStatus && $requestStatus == 'pending')
                    <button class="btn btn-danger text-uppercase fw-bold px-4 py-3">Request already Submitted</button>
                @elseif($requestStatus && $requestStatus === 'approved')
                    <button type="button" class="btn btn-primary px-5 py-3 text-uppercase fw-bold" wire:click="download">
                        <span wire:loading.remove wire:target="download">
                            Download Payslip <i class="fa-solid fa-download ms-2"></i>
                        </span>
                        <span wire:loading wire:target="download">
                            Downloading <i class="fa-solid fa-spinner ms-2 fa-spin"></i>
                        </span>
                    </button>
                @else
                    <button type="button" class="btn btn-primary px-5 py-3 text-uppercase fw-bold" wire:click="request">
                        <span wire:loading.remove wire:target="request">
                            Request Download <i class="fa-solid fa-file-arrow-down ms-2"></i>
                        </span>
                        <span wire:loading wire:target="request">
                            Sending Request <i class="fa-solid fa-spinner ms-2 fa-spin"></i>
                        </span>
                    </button>
                @endif
            </div>
            <div>
                <div class="payslip-container">
                    @include('employee.payslip-main', [
                        'payroll' => $payroll
                    ])
                </div>
            </div>
        @endif
    </div>
</div>