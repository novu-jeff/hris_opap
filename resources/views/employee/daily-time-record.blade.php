@extends('layouts.employee', [
    'title' => $title
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1>{{$header}}</h1>
            <p>{{$sub}}</p>
        </div>
        <div class="action">
            <a href="{{route('employee.dashboard')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
        </div>
    </div>
    <div class="mt-3">
        @livewire('employee.daily-time-record', [
            'month' => $month,
            'year' => $year,
        ])
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


<script>
      $(document).on('click', '.save-as-pdf', function() {
        var element = $('.dtr')[0];  
        var printWindow = window.open('', '_blank', 'width=800,height=600');

        printWindow.document.write('<html><head><title>Print</title>');
        
        printWindow.document.write('<style>');
        printWindow.document.write(`
            /* Add any custom styling here */
            .underline {
                text-decoration: underline;
            }
            .dtr {
                width: 100%;
                margin: 0 auto;
                padding: 10mm 5mm;
                box-sizing: border-box;
                border: 1px solid rgb(178, 178, 178);
                background-color: #fdffe4;
                border-radius: 12px;
                position: relative;
            }

            .dtr-header {
                position: relative;
                text-align: center;
                margin-bottom: 20px;
            }

            .dtr-header img {
                position: absolute;
                top: -10px;
                left: 30px;
                height: 70px;
            }

            @media(max-width: 993px ) {
                .dtr-header img {
                    left: 0;
                }
            }

            .dtr-header h1 {
                font-size: 14px;
                margin: 5px 0;
            }

            .dtr-info {
                margin-bottom: 20px;
                font-size: 12px;
            }

            .dtr-info div {
                margin-bottom: 5px;
            }

            .dtr-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px;
            }

            .dtr-table th, .dtr-table td {
                border: 1px solid black;
                text-align: center;
                padding: 5px;
            }

            .dtr-summary {
                margin-top: 20px;
                font-size: 10px;
            }
            .dtr-summary td {
                text-align: left
            }

            .dtr-summary .signature {
                margin-top: 20px;
                text-align: center;
                font-size: 10px;
            }

            .remarks {
                margin-top: 20px;
                font-size: 10px;
            }
            .shaded-box {
                position: absolute;
                top: 0;
                right: 0;
                padding: 5px;
            }
            @media print {
                .dtr {
                    width: 100%;
                    page-break-before: always;
                }

                .dtr-table th, .dtr-table td {
                    font-size: 8px; 
                    padding: 2px;  
                }

                .dtr-header img {
                    height: 50px; 
                }

                @page {
                    margin: 10mm;
                }
            }
        `);
        printWindow.document.write('</style>');
        
        printWindow.document.write('</head><body>');
        printWindow.document.write(element.outerHTML); 
        printWindow.document.write('</body></html>');

        printWindow.document.close(); 
        printWindow.print();  
        printWindow.close(); 
    });
  </script>
  
@endsection