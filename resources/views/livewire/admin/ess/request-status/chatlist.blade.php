<div class="chat-list" wire:poll="loadRecords">
    @forelse ($employees as $employee)
        <a href="javascript:void(0)" wire:click="select({{$employee->id}})" class="d-flex align-items-center {{$selected_id === $employee->id ? 'selected' : ''}}">
            <div class="flex-shrink-0">
                <img src="
                   {{ isset($records['user']) && $records['user']['personal']['profile']
                        ? Storage::url('employee/users/' . $records['user']['employee_id'] . '/' . $records['user']['profile']) 
                        : 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=10' 
                    }}" style="width: 45px; height: 45px;">
            </div>
            <div class="flex-grow-1 ms-3">
                <h3>{{ucwords($employee->personal->firstname . ' ' . $employee->personal->lastname)}}</h3>
                <p>{{ucwords($employee->positions->name)}}</p>
            </div>
            <div wire:ignore.self>
                @if($employee['unseen_count'] > 0)
                    @if($employee['unseen_count'] > 10)
                        <span class="badge bg-primary">10+</span>
                    @else
                        <span class="badge bg-primary">{{$employee['unseen_count']}}</span>
                    @endif
                @endif
            </div>
        </a>
    @empty
        <div class="alert alert-info text-uppercase fw-bold text-center">No Employees</div>
    @endforelse
</div>