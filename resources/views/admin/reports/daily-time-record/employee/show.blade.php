@extends('layouts.admin', [
    'title' => 'HRIS | DTR'
])

@section('content')
<div class="main-content flex-grow-1 p-4">
<div class="pb-5">
    @livewire('admin.reports.daily-time-record.employee.show', ['employee_no' => $employee_no, 'month' => $month, 'year' => $year])
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
                * {
                    box-sizing: border-box;
                }
                body {
                    margin: 0;
                    padding: 10px;
                    font-family: Arial, sans-serif;
                    zoom: 0.75; /* adjust between 0.75–0.9 if needed */
                }
                
                 .dtr-header img {
                    width: 60px;   /* CHANGE SIZE HERE */
                    height: auto;
                }
                
                .dtr-header {
                    position: relative;
                    text-align: center;
                    margin-bottom: 5px;
                }
    
                .dtr-info {
                    margin-bottom: 8px;
                    font-size: 12px;
                }
    
                .underline {
                    min-width: auto;
                    width: fit-content;
                    border-bottom: 1px solid black;
                    padding: 0 10px 0 20px;
                    display: inline-flex;
                    align-items: end;
                }
                
    
                .dtr-container {
                    display: flex;
                    width: 100%;
                    page-break-inside: avoid;
                }
    
                .dtr {
                    padding: 5mm 3mm;
                    border: 1px solid #999;
                    border-radius: 0px;
                    background: white;
                    font-size: 8px;
                    page-break-inside: avoid;
                    width: 49%;
                }
    
                .dtr-table {
                    width: 100%;
                    border-collapse: collapse;
                }
    
                .dtr-table th, .dtr-table td {
                    border: 1px solid #555;
                    padding: 1px;
                    font-size: 6.5px;
                    text-align: center;
                }
    
                .dtr-summary {
                    text-align: center;
                    margin-top: 5px;
                }
                .dtr-summary h5 {
                    text-transform: uppercase;
                    font-weight: bold;
                    margin: 10px 0;
                    font-size: 10px;
                }  
                .dtr-summary-container {
                    width: 90%;
                    margin: auto;
                    display: grid;
                    grid-template-columns: repeat(3, minmax(200px, 1fr));
                    text-align: left;
                    gap: 0;
                } 
                
                .dtr-summary-item {
                    font-size: 9px;
                    font-weight: 600;
                    margin-bottom: 2px;
                    text-transform: uppercase;
                    color: #000000c5;
                }
    
            .text-uppercase {
                text-transform: uppercase !important;
            }
    
                @page {
                    size: A4 landscape;
                    margin: 5mm;
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