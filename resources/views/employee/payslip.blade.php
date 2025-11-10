@extends('layouts.employee', [
    'title' => $title
])

@section('content')
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1>{{$header}}</h1>
        </div>
        <div class="action">
            @if ($action === 'index')
                <div class="d-md-flex gap-3">
                    <a href="{{route('employee.dashboard')}}" class="btn btn-outline-primary text-uppercase px-5 py-3 fw-medium">Go Back</a>
                </div>
            @endif
        </div>
    </div>
    <div class="mt-3" id="payslip-content">
        @livewire('employee.payslip')
    </div>
</div>
@endsection
@section('script')
<script>
    $(function () {
        Livewire.on('download-payslip', async (event) => {
            const data = event[0];
            const allowDownload = data.allowDownload;
            const filename = data.filename;
            if (allowDownload) {
                document.querySelectorAll('.controls').forEach(el => el.remove());

                const { jsPDF } = window.jspdf;
                const content = document.getElementById("payslip-content");

                if (!content) {
                    alert("Payslip content not found!");
                    return;
                }

                try {
                    const canvas = await html2canvas(content, { scale: 2 });
                    const imgData = canvas.toDataURL("image/png");

                    const pdf = new jsPDF({
                        orientation: "portrait",
                        unit: "px",
                        format: [canvas.width, canvas.height],
                    });

                    pdf.addImage(imgData, "PNG", 0, 0, canvas.width, canvas.height);
                    pdf.save(filename);

                } catch (error) {
                    console.error("Error generating PDF:", error);
                    alert("An error occurred while generating the PDF.");
                }
            }
        });
    });
</script>
@endsection
