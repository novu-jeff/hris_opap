@extends('layouts.admin', [
    'title' => 'HRIS | All Shift Schedules'
])

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Daily Time Record > <span  class="text-primary">{{ $date }}</span></h1>
        </div>
        <div class="actions">
            <a href="{{ route('reports.dtr') }}" class="btn btn-outline-danger text-uppercase px-5 py-3 fw-medium">Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.daily-time-record.employee.index', ['date' => $date])
    </div>
</div>
@endsection


@section('script')
<script>
    function printDTR() {
        var printContent = document.getElementById('dtrContent').innerHTML; // Get content by ID
        var printWindow = window.open('', '', 'height=500,width=800'); // Open a new window
        
        // Write HTML structure for printing
        printWindow.document.write('<html><head><title>Print DTR</title>');
        
        // Add CSS for print styling to fit on bond paper
        printWindow.document.write(`
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    text-align: center;
                }
                .dtr {
                    width: 100%;
                    max-width: 100%;
                    margin: 0 auto;
                    padding: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                }
                /* Fit content on one page */
                @page {
                    size: A4;
                    margin: 0.5cm;
                }
                @media print {
                    body {
                        font-size: 12px;
                    }
                    .dtr-header {
                        text-align: center;
                    }
                    .dtr-info {
                        text-align: left;
                    }
                    .dtr-table {
                        margin: 20px auto;
                    }
                    table {
                        page-break-inside: avoid;
                    }
                    /* Ensure no page breaks inside the table */
                }
            </style>
        `);
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContent); // Insert the content to be printed
        printWindow.document.write('</body></html>');
        printWindow.document.close(); // Close the document for printing

        printWindow.print(); // Trigger the print dialog
    }
</script>
@endsection