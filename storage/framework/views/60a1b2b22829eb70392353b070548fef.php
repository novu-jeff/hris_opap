<?php $__env->startSection('content'); ?>
<div class="main-content flex-grow-1 p-4">
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Employee Records</h1>
            <p></p>
        </div>
    </div>
    <div class="mt-3">
        <iframe src="https://docs.google.com/spreadsheets/d/e/2PACX-1vS6zd4G00InrzT0frNY2U5BR770JzPiTZJJQE4z1PA8iNGrLxIUCTZM1wPNgqi7CA/pubhtml?widget=true&amp;headers=false"></iframe>
        
    <style>
        iframe {
            width: 100%;
            height: 100vh;
        }
    </style>
</div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', [
    'title' => 'HRIS | Staffing Records'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/admin/hris/staffing.blade.php ENDPATH**/ ?>