<div class="row" wire:ignore.self>
    <div class="col-12">
        <div class="chat-area">
            <div class="chatlist">
                <div class="chat-header px-3 py-2 border-bottom">
                    <div class="msg-search d-block p-2 text-center py-3">
                        <h5 class="mb-0 fw-bold text-center text-uppercase">All Employees</h5>
                    </div>
                </div>
                <div class="scroll">
                    @livewire('admin.ess.request-status.chatlist')
                </div>
            </div>
            <div class="chatbox active">
                @livewire('admin.ess.request-status.chatbox')
            </div>
        </div>
    </div>
</div>