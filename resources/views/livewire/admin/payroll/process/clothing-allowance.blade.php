<div>
    <div class="action mb-4">
        <div class="d-md-flex justify-content-end gap-3">
            <a href="{{route('payroll.index', [
                'type' => $type,
                'employment_type' => $employment_type
            ])}}" 
            class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <hr class="mt-0">
    <div class="text-uppercase fw-bold">
        @if($isApproved)
            <h2 class="text-success fw-bold text-uppercase text-center">Approved</h2>
        @else
            <h2 class="text-danger fw-bold text-uppercase text-center">Pending</h2>
        @endif
    </div>
    <hr>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                Type : <span class="ms-2">{{$records['payroll']['type']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Date : <span class="ms-2">{{$records['payroll']['formatted_payroll_date']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Employee Type : <span class="ms-2">{{$records['payroll']['formatted_employment_type']}}</span>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                No. of employees : <span class="ms-2">{{$records['payroll']['no_employees']}}</span>
            </div>
            <div class="text-uppercase fw-bold">
                Total Amount : <span class="ms-2">PHP {{number_format($records['payroll']['total_allowances'], 2)}}</span>
            </div>
        </div>
    </div>
    <hr class="pt-3">

    @if($product == 'government')
        <div class="table-responsive pb-3">
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th rowspan="2">No.</th>
                        <th rowspan="2" class="text-center">Name</th>
                        <th rowspan="2" class="text-center">Position</th>
                        <th rowspan="2" class="text-center">Date Hired</th>
                        <th rowspan="2" class="text-center">Clothing Allowance</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($records['payroll_items'] as $sectionIndex => $sectionGroup)
                        <tr class="fw-bold bg-primary text-white sticky-top" style="top: 55px; z-index: 9;">
                            <td colspan="100%">
                                <div class="d-flex justify-content-between w-100 px-5">
                                    <span class="text-center flex-grow-1">{{ $sectionGroup['section_name'] ?? 'Unknown Section' }}</span>
                                </div>
                            </td>
                        </tr>

                        @foreach($sectionGroup['employees'] as $employeeIndex => $record)
                            <tr>
                                 <td>
                                    <div class="marked-changed">
                                        @if(in_array($record['id'], $updatedItems))
                                            <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                        @else
                                            <i class="fa-solid fa-check ready" title="No changes made"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    #{{ $employeeIndex + 1 }}
                                </td>
                                <td>
                                    <a href="{{ route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information']) }}"
                                    class="text-dark" target="_blank">
                                        {{ $record['name'] }}
                                    </a>
                                </td>
                                <td>{{ $record['position'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($record['date_hired'])->format('M d, Y') }}</td>
                                <td>
                                    <input type="number" wire:change="recompute({{ $sectionIndex }}, {{ $employeeIndex }})"
                                        wire:model="allowance.{{ $sectionIndex }}.{{ $employeeIndex }}"
                                        class="form-control" style="width: 120px;">
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="12" class="py-3 text-uppercase fw-bold text-muted">
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($hasChanges)
        <div class="d-flex justify-content-end mt-5">
            <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="save">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">
                    Saving <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    @endif

    @if(!$isApproved && !$hasChanges)
        <div class="d-flex justify-content-end mt-5">
            <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="approve">
                <span wire:loading.remove wire:target="approve">Approve</span>
                <span wire:loading wire:target="approve">
                    Please Wait <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    @endif
</div>