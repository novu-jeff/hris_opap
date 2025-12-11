@extends('layouts.employee', [
    'title' => $title
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
        
    </div>
    <div class="mt-3">
        @livewire('employee.daily-time-record', [
            'month' => $month,
            'year' => $year,
        ])
    </div>
</div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>



<script>
$(document).on('click', '.save-as-pdf', function() {

    var original = $('.dtr').first();  
    if (original.length === 0) {
        alert("No DTR found.");
        return;
    }

    // CLONE DTR
    var copy = original.clone();

    // 🔥 FIX LOGO FOR BOTH ORIGINAL & COPY
    [original, copy].forEach(function(section) {
        section.find('img').each(function() {
            var src = $(this).attr('src');
            if (src && !src.startsWith('http')) {
                $(this).attr('src', window.location.origin + src);
            }
        });
    });

    var printWindow = window.open('', '_blank', 'width=900,height=700');

    printWindow.document.write('<html><head><title>{{$employee_no . " | DTR"}}</title>');

    printWindow.document.write(`
        <style>
            body {
                margin: 0;
                padding: 10px;
                font-family: Arial, sans-serif;
            }
            
             .dtr-header img {
                width: 80px;   /* CHANGE SIZE HERE */
                height: auto;
            }
            
            .dtr-header {
                position: relative;
                text-align: center;
                margin-bottom: 20px;
            }

            .dtr-info {
                margin-bottom: 20px;
                font-size: 14px;
            }

            .underline {
                min-width: 300px;
                width: fit-content;
                border-bottom: 1px solid black;
                padding: 0 10px 0 20px;
                display: inline-flex;
                align-items: end;
            }
            

            .dtr-container {
                display: grid;
                grid-template-columns: 50% 50%;
                gap: 10px;
                width: 100%;
            }

            .dtr {
                padding: 10mm 5mm;
                border: 1px solid #999;
                border-radius: 10px;
                background: white;
                font-size: 9px;
                page-break-inside: avoid;
            }

            .dtr-table {
                width: 100%;
                border-collapse: collapse;
            }

            .dtr-table th, .dtr-table td {
                border: 1px solid #555;
                padding: 3px;
                font-size: 8px;
                text-align: center;
            }

            .dtr-summary {
                text-align: center;
                margin-top: 20px;
            }
            .dtr-summary h5 {
                text-transform: uppercase;
                font-weight: bold;
                margin: 30px 0 30px 0;
            }  
            .dtr-summary-container {
                width: 90%;
                margin: auto;
                display: grid
            ;
                grid-template-columns: repeat(3, minmax(200px, 1fr));
                text-align: left;
            } 
            
            .dtr-summary-item {
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 0px !important;
                text-transform: uppercase;
                color: #000000c5;
            }

        .text-uppercase {
            text-transform: uppercase !important;
        }

            @page {
                size: A4 landscape;
                margin: 8mm;
            }
        </style>
    `);

    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="dtr-container">');

    // LEFT copy
    printWindow.document.write(original.prop('outerHTML'));

    // RIGHT copy
    printWindow.document.write(copy.prop('outerHTML'));

    printWindow.document.write('</div></body></html>');

    printWindow.document.close();

    // Wait for images to load before printing
    printWindow.onload = function () {
        printWindow.print();
        setTimeout(() => printWindow.close(), 500);
    };
});
</script>

  
@endsection