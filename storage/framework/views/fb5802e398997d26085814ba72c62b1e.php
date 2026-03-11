<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="mt-5 d-lg-flex justify-content-between align-items-start">
        <div class="section-title">
            <h1><?php echo e($header); ?></h1>
        </div>
       
    </div>
    <div class="mt-3" id="payslip-content">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('employee.payslip');

$__html = app('livewire')->mount($__name, $__params, 'lw-1290576481-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', [
    'title' => $title
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/employee/payslip.blade.php ENDPATH**/ ?>