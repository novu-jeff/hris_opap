<div class="notification" wire:poll="loadNotifications" wire:poll.keep-alive wire:visible>
    <div wire:click="toggle">
        <i class="fa-regular fa-bell"></i>
        @if($notifications['unread'] > 0)
            <span class="count">{{$notifications['unread']}}</span>
        @endif
    </div>
    <div class="content {{$isOpened ? 'd-block' : 'd-none'}}">
        <div class="header">
            <h6 class="m-0">All Notifications</h6>
        </div>
        <div class="scrollable" id="notificationList">
            @forelse ($notifications['data'] as $key => $item)
                @php
                    $data = json_decode($item['data'], true);
                @endphp
                <div wire:click="read('{{$item['id']}}', '{{$data['redirect']}}')" class="item nav-link {{is_null($item['read_at']) ? 'active' : ''}}">
                    <div class="d-flex gap-3">
                        <div class="icon">
                            @if($data['type'] == 'info')
                                <i class="fa-regular fa-lightbulb" style="color: #d3b404"></i>
                            @elseif($data['type'] == 'success')
                                <i class="fa-regular fa-thumbs-up" style="color: #225F8B"></i>
                            @elseif($data['type'] == 'error')
                                <i class="fa-solid fa-triangle-exclamation" style="color: #dc3545"></i>
                            @elseif($data['type'] == 'message')
                                <i class="fa-regular fa-message" style="color: #225F8B"></i>
                            @endif
                        </div>
                        <div class="message">
                            <div class="mb-2"> {!! $data['message'] !!}</div>
                            <small class="text-muted fw-medium">(click this notification to view more details)</small>
                        </div>
                    </div>
                    <div class="timestamp">
                        <small>{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <p class="text-uppercase text-center p-3 mt-4">No notifications available.</p>
            @endforelse
        </div>
    </div>
</div>

@section('script')
<script>
  $(function() {
        var notificationList = $('#notificationList');
        notificationList.on('scroll', function() {
            if (notificationList.scrollTop() + 300) {
                Livewire.dispatch('loadNotifications');
            }
        });
    });

</script>
@endsection
