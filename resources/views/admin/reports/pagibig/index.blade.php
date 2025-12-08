@extends('layouts.admin', [
    'title' => 'Reports | Pagibig',
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Pagibig Report </h1>
        </div>
    </div>
    <div class="mt-3">
        @livewire('admin.reports.pagibig.index')
    </div>
</div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    $(document).on('click', '#printButton', function() {
        console.log('Print button clicked');

        var elements = $('.table-content');  
        var printWindow = window.open('', '_blank', 'width=800,height=600');

        printWindow.document.write('<html><head><title></title>');
        
        printWindow.document.write('<style>');
             printWindow.document.write(`
        body {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: none;
        }
        th, td {
            padding: 6px;
            border: 1px solid #dee2e6;
            text-align: left;
            font-size: 10px !important;
            border: none;
        }
        th {
            background-color: #f8f9fa;
            border: none;
        }
            
        .fw-bold {
            font-weight: bold;
        }
        .bg-success {
            background-color: #d1e7dd !important;
        }
        .bg-opacity-25 {
            opacity: 0.85;
        }
        h5 {
            font-weight: 800;
            margin-top: 30px;
        }
    `);
        printWindow.document.write('</style>');

        printWindow.document.write('</head><body>');
        
        printWindow.document.write('<div class="dtr-container">');
        
        printWindow.document.write(elements[0].outerHTML);

        if (elements.length > 1) {
            printWindow.document.write(elements[1].outerHTML);
        }

        printWindow.document.write('</div>'); 
        printWindow.document.write('</body></html>');

        printWindow.document.close(); 
        printWindow.print();  
        printWindow.close(); 
    });
</script>
@endsection