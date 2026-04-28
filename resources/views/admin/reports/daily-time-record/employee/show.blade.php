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
$(document).on('click', '.save-as-pdf', function () {
    let content = $('#print-template').html();

    let printWindow = window.open('', '_blank', 'width=1200,height=800');

    printWindow.document.write(`
        <html>
        <head>
            <title>DTR Print</title>

            <style>
                /* PDF paper margin */
                @page {
                    size: A4 portrait;
                    margin: 15mm 12mm 15mm 12mm;
                }

                * {
                    box-sizing: border-box;
                }

                html,
                body {
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    font-size: 10px;
                    background: white;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* visible content margin */
                body {
                    padding: 10px;
                }

                .dtr-container {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    gap: 12px;
                    width: 100%;
                }

                .dtr-copy {
                    width: 48%;
                    page-break-inside: avoid;
                    break-inside: avoid;
                }

                .logo {
                    width: 42px !important;
                    height: auto !important;
                    display: block;
                    margin: 0 auto 5px auto;
                }

                .office-title {
                    text-align: center;
                    font-size: 11px;
                    font-weight: bold;
                    line-height: 1.2;
                    margin-bottom: 8px;
                }

                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 6px;
                    font-size: 10px;
                }

                .info-table td {
                    border: none;
                    padding: 1px;
                    text-align: left;
                    vertical-align: top;
                }

                .dtr-table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                }

                .dtr-table th,
                .dtr-table td {
                    border: 1px solid #000;
                    text-align: center;
                    padding: 2px;
                    font-size: 9px;
                    line-height: 1.1;
                }

                .signature {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 10px;
                }

                .line {
                    border-top: 1px solid #000;
                    margin-top: 18px;
                    padding-top: 3px;
                    font-weight: bold;
                }
            </style>
        </head>

        <body>
            ${content}
        </body>
        </html>
    `);

    printWindow.document.close();

    printWindow.onload = function () {
        printWindow.focus();
        printWindow.print();

        setTimeout(() => {
            printWindow.close();
        }, 800);
    };
});
    </script>
    
      
    @endsection