@extends('layouts.admin', [
    'title' => 'HRIS | All Shift Schedules'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container mt-4">
    <h1>Upload Leave Data (XLSX)</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('leave.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Select File (.xlsx)</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Import Type</label>
            <select name="import_type" class="form-select" required>
                <option value="">-- Select Type --</option>
                <option value="vl_sl">Employee Leave Cards (VL/SL history)</option>
                <!--<option value="credits">Leave Credits (summary)</option>-->
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Employee No (required)</label>
            <input type="text" name="employee_no" class="form-control" placeholder="For single employee import only">
        </div>

       <!-- <div class="mb-3">
            <label class="form-label">Leave Type ID (for credits import only)</label>
            <input type="number" name="leave_type_id" class="form-control" placeholder="1=VL, 2=SL, etc.">
        </div>-->

        <button class="btn btn-primary">Upload and Import</button>
    </form>
</div>
<div class="main-content flex-grow-1 p-4">
@endsection
