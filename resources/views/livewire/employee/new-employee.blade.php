<div>
    @if($isNewEmployee || $isForcedEmployee)
    <div class="change-password">
        <div class="content" wire:ignore.self>
            <div class="card px-3 py-3 shadow rounded-4">
                <div class="card-header pt-3 border-0 bg-transparent">
                    <h4 class="mt-4 mb-3 fw-bold">Hello <span>{{ucfirst($name)}}</span>,</h4>
                    <p class="text-justify mb-0">
                        @if($isNewEmployee)
                            As part of our commitment to ensuring the security of your personal information, we kindly request that you update your password before accessing the employee self-service portal. This step is essential to protect your account and maintain the integrity of our system. Thank you for your cooperation and understanding.
                        @elseif($isForcedEmployee)
                            Your account password has been already exceeded for 30 days. As part of our commitment to security, you are required to update your password before accessing the employee self-service portal. This step is essential to protect your account and maintain the integrity of our system. Thank you for your cooperation and understanding.
                        @endif
                    </p>
                </div>
                <hr>
                <div class="card-body">
                    <form wire:submit.prevent="save" wire:target="save">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="mb-2" for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model="password" placeholder="••••••••">
                                <div class="error-field mt-2">
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="mb-2" for="confirm-password">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model="confirm_password" placeholder="••••••••">
                                <div class="error-field mt-2">
                                    @error('confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                                <span wire:loading.remove wire:target="save">Change Password</span>
                                <span wire:loading wire:target="save">Changing <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if($isNewEmployee || $isForcedEmployee)
@section('script')
<script>
    $(function() {
        $('body').addClass('overflow-hidden');
        setTimeout(() => {
            $('.change-password .content').fadeIn();
        }, 200);
    });
</script>
@endsection
@endif