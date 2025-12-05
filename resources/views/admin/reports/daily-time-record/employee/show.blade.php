@extends('layouts.admin', [
    'title' => 'HRIS | DTR'
])

@section('content')
<div class="pb-5">
    @livewire('admin.reports.daily-time-record.employee.show', ['employee_no' => $employee_no, 'month' => $month, 'year' => $year])
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


<script>
    $(document).on('click', '.save-as-pdf', function() {

        var elements = $('.dtr');  
        var printWindow = window.open('', '_blank', 'width=800,height=600');

        printWindow.document.write('<html><head><title>{{$employee_no . ' | DTR'}}</title>');
        
        printWindow.document.write('<style>');
        printWindow.document.write(`

        .underline {
                text-decoration: underline;
            }

            .dtr {
                max-width: 100%;
                width: 100%;
                margin: 0;
                padding: 10mm 5mm;
                box-sizing: border-box;
                border: 1px solid rgb(178, 178, 178);
                background-color: #fff;
                border-radius: 12px;
                position: relative;
                font-size: 8px;
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

            @media(max-width: 993px) {
                .dtr-header img {
                    left: 0;
                }
            }

            .dtr-header h1 {
                font-size: 10px;
                margin: 5px 0;
            }

            .dtr-info {
                display: grid;
                grid-template-columns: repeat(2, 1fr); /* Create two equal columns */
                gap: 10px; /* Space between grid items */
                margin-bottom: 20px;
                font-size: 8px;

                div {
                    margin: 0 !important;
                }

            }

            .dtr-info div {
                margin-bottom: 0; /* Remove margin between divs */
            }


            .dtr-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 8px;
            }

            .dtr-table th, .dtr-table td {
                border: 1px solid rgb(178, 178, 178);
                text-align: center;
                padding: 5px;
            }

            .dtr-summary {
                margin-top: 20px;
                font-size: 8px;
            }

            .dtr-summary td {
                text-align: left;
            }

            .dtr-summary .signature {
                margin-top: 20px;
                text-align: center;
                font-size: 8px;
            }

            .remarks {
                margin-top: 20px;
                font-size: 8px;
            }

            .shaded-box {
                position: absolute;
                top: 0;
                right: 0;
                padding: 5px;
            }

            /* Print-specific styles */
            @media print {
                /* Create a 2-column grid layout for the two .dtr elements */
                .dtr-container {
                    display: grid;
                    grid-template-columns: 48% 48%; /* Two columns, each 48% width */
                    gap: 4%; /* Space between the two elements */
                    margin: 0 auto;
                    page-break-before: always;
                }

                .dtr {
                    width: 100%;
                    padding: 5mm 3mm; /* Adjust padding for print */
                }

                .dtr-header img {
                    height: 50px;
                }

                .dtr-table th, .dtr-table td {
                    font-size: 8px;
                    padding: 2px;
                }

                .btn-correction {
                    display: none;
                }

                @page {
                    margin: 10mm;
                }
            }

            .dtr-summary-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }
        `);
        printWindow.document.write('</style>');

        printWindow.document.write('</head><body>');
        
        printWindow.document.write('<div class="dtr-container test-class">');
        
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