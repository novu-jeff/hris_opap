<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="row mb-3 mt-5">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label for="entries" class="form-label mb-0">Show entries:</label>
                    <select id="entries" wire:model.change="entries" class="form-select w-auto">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                        <option value="60">60</option>
                        <option value="70">70</option>
                        <option value="80">80</option>
                        <option value="90">90</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                    <label for="search" class="form-label mb-0">Filter Status:</label>
                    <select wire:model.change="status" id="status" class="form-select w-50">
                        <option value=""> - ALL - </option>
                        <option value="pending"> Pending </option>
                        <option value="approved"> Approved </option>
                        <option value="disapproved"> Disapproved </option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Payroll Date</th>
                            <th>Cut Off Period</th>
                            <th>Status</th>
                            <th style="max-width: 200px;">Action</th>
                        </tr>
                    </thead>                
                    <tbody>
                        @forelse($records as $record)
                            <tr data-id="{{$record->id}}">
                                <td>#{{format_id($record->id, 6)}}</td>
                                <td>{{\Carbon\Carbon::parse($record->payroll_date)->format('F d, Y')}}</td>
                                <td>
                                    @php
                                        $dates = explode(' to ', $record->cut_off_period);
                                        $startDate = \Carbon\Carbon::parse($dates[0])->format('F d, Y');
                                        $endDate = \Carbon\Carbon::parse($dates[1])->format('F d, Y');
                                    @endphp

                                    {{ $startDate }} - {{ $endDate }}
                                </td>
                                <td>{{$record->status}}</td>
                                <td>
                                    <a href="{{route('payroll.process', ['payroll_id' => $record->id])}}" class="btn btn-primary">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                            </tr> 
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $records->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" data-bs-backdrop="static" id="newPayroll" tabindex="-1" aria-labelledby="newPayrollLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase fw-medium" id="newPayrollLabel">Create New Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="createPayroll">
                        <div class="mb-3">
                            <label for="cutOffPeriod" class="form-label">Cut Off Period</label>
                            <input type="text" class="form-control" id="daterangepicker" wire:model='cut_off_period' id="cut_off_period" wire:model="cutOffPeriod">
                            <div class="error-field">
                                @error('cut_off_period') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="payrollDate" class="form-label">Payroll Date</label>
                            <input type="date" class="form-control"  wire:model='payroll_date' id="payroll_date" wire:model="payrollDate">
                            <div class="error-field">
                                @error('payroll_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="employmentType" clsass="form-label">Employment Type</label>
                            <select name="employment_type" id="employment_type" wire:model="employment_type" class="form-select mt-2">
                                <option value=""> - CHOOSE - </option>
                                @foreach($employmentTypes as $type)
                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                            <div class="error-field">
                                @error('payroll_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary px-5 py-3 text-uppercase">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
    <script>
        $(function() {

            $('#daterangepicker').daterangepicker({
                locale: { format: 'YYYY-MM-DD' },
                autoUpdateInput: false
            });

            $('#daterangepicker').on('apply.daterangepicker', function(ev, picker) {
                @this.set('cut_off_period', picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            });

        });
    </script>
@endsection