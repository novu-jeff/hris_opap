<div class="row" wire:ignore.self>
    <div class="col-12">
        <div class="chat-area">
            <div class="chatlist">
                <div class="modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="chat-header px-3 py-2 border-bottom">
                            <div class="msg-search p-2">
                                <h5 class="mb-0 text-uppercase fw-bold">All Employees</h5>
                            </div>
                        </div>
                        <div class="modal-body mt-3">
                            @livewire('admin.ess.request-status.chatlist')
                        </div>
                    </div>
                </div>
            </div>
            <div class="chatbox active">
                @livewire('admin.ess.request-status.chatbox')
            </div>
        </div>
    </div>
</div>