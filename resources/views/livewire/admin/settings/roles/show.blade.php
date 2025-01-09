<div>
    <form wire:submit.prevent="savePermissions">
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">Module</th>
                    <th class="px-4 py-2 border">Action</th>
                    <th class="px-4 py-2 border">Read</th>
                    <th class="px-4 py-2 border">Write</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $module => $actions)
                @foreach ($actions as $action)
                    <tr>
                        <td class="px-4 py-2 border">{{ ucfirst($module) }}</td>
                        <td class="px-4 py-2 border">{{ ucfirst($action) }}</td>
                        
                        <!-- Read Permission -->
                        <td class="px-4 py-2 border">
                            <input 
                                type="checkbox" 
                                id="read-{{ $module . '.' . $action }}"
                                class="permission-checkbox"
                                data-module="{{ $module }}"
                                data-action="{{ $action }}"
                                data-permission="read"
                                {{ isset($selectedPermissions["$module.$action"]['read']) && $selectedPermissions["$module.$action"]['read'] ? 'checked' : '' }}
                                onclick="updatePermission(event)" />
                        </td>
                        
                        <!-- Write Permission (Only show write checkbox for actions other than 'my-directory', 'my-team', 'employee-announcements', 'payslip') -->
                        <td class="px-4 py-2 border">
                            @if (!in_array($action, ['my-directory', 'my-team', 'employee-announcements', 'payslip']))
                                <input 
                                    type="checkbox" 
                                    id="write-{{ $module . '.' . $action }}"
                                    class="permission-checkbox"
                                    data-module="{{ $module }}"
                                    data-action="{{ $action }}"
                                    data-permission="write"
                                    {{ isset($selectedPermissions["$module.$action"]['write']) && $selectedPermissions["$module.$action"]['write'] ? 'checked' : '' }}
                                    onclick="updatePermission(event)" />
                            @else
                                <!-- If the action is in the list, don't show the write permission checkbox -->
                                <input type="hidden" id="write-{{ $module . '.' . $action }}">
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
        <div class="mt-5 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase fw-bold">
                <span wire:loading.remove wire:target="savePermissions">Save Permissions <i class="fa-solid fa-arrow-right ms-2"></i></span>
                <span wire:loading wire:target="savePermissions">Saving Permissions <i class="fa-solid fa-spinner ms-2 fa-spin"></i></span>
            </button>
        </div>
    </form>
</div>

<script>
    // Custom JavaScript to handle checkbox state changes
    function updatePermission(event) {
        let checkbox = event.target;
        let module = checkbox.dataset.module;
        let action = checkbox.dataset.action;
        let permission = checkbox.dataset.permission;

        const data = [
            {
                checkbox: checkbox,
                module: module,
                action: action,
                permission: permission,
                value: checkbox.checked
            }
        ];

        Livewire.dispatch('updatePermissions', data);
    }
</script>
