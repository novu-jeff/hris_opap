<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Payroll Records</h1>
        </div>
    </div>
    <div class="mt-3">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.reports.payroll.view', [
            'payrollId' => $payrollId
        ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-2269870611-0', $__slots ?? [], get_defined_vars());

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

<style>
.table-responsive {
    overflow: visible !important; /* allow sticky to escape the scroll container */
}

tfoot tr.table-total-row {
    position: sticky;
    bottom: 0;
    background-color: #ffd966 !important; /* bright yellow */
    color: #000 !important;
    font-weight: bold;
    z-index: 5;
}

 .info-row {
        display: flex;
        /*justify-content: space-between;
        gap: 12px;*/
    }
    .info-label {
        min-width: 120px; /* adjust if needed */
        color: #555;
    }

    .info-label-3 {
        min-width: 180px; /* adjust if needed */
        color: #555;
    }
    .info-value {
        text-align: right;
        white-space: nowrap;
    }
.payroll-table-wrapper {
    max-height: 600px; /* Adjust the table height */
    overflow-y: auto;
    position: relative;
}

.table-header th {
    position: sticky;
    top: 50;
    z-index: 10;
}

.table-footer {
    position: sticky;
    bottom: 0;
    z-index: 10;
}

.table-footer td {
    background-color: #f8f9fa; /* Same as table-footer bg-light */
}


</style>
<?php echo $__env->make('layouts.admin', [
    'title' => 'HRIS | Payroll Record'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/admin/reports/payroll/view.blade.php ENDPATH**/ ?>