<div>
    <div>
    <div class="modal fade"
         id="show"
         wire:ignore.self
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-scrollable" >
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-uppercase fw-bold">
                        Salary Table – <?php echo e($show->name ?? ''); ?>

                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!--[if BLOCK]><![endif]--><?php if($show): ?>
                        
                       <!-- <div class="mb-3">
                            <input type="checkbox" wire:model="showWtax" id="showWtax">
                            <label for="showWtax" class="form-label mb-0">Show WTAX</label>
                        </div>-->

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th rowspan="2">Salary Grade</th>
                                        <th colspan="8">Steps</th>
                                    </tr>
                                    <tr>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $show->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                     <?php
                                        $gradeBg = $loop->even ? 'bg-primary bg-opacity-25' : 'bg-primary bg-opacity-10';
                                    ?>
                                                                        
                                        <tr class="fw-semibold">
                                            <td rowspan="<?php echo e($showWtax ? '2' : '1'); ?>" class="text-center <?php echo e($gradeBg); ?>">
                                                <?php echo e($data['salary_grade']); ?>

                                            </td>

                                            <td>₱<?php echo e(number_format((float) $data['step_1'], 2)); ?></td>
                                            <td>₱<?php echo e(number_format((float) $data['step_2'], 2)); ?></td>
                                            <td>₱<?php echo e(number_format((float) $data['step_3'], 2)); ?></td>
                                            <td>₱<?php echo e(number_format((float) $data['step_4'], 2)); ?></td>
                                            <td>₱<?php echo e(number_format((float) $data['step_5'], 2)); ?></td>
                                            <td>₱<?php echo e(number_format((float) $data['step_6'], 2)); ?></td>
                                            <td>₱<?php echo e(number_format((float) $data['step_7'], 2)); ?></td>
                                            <td>₱<?php echo e(number_format((float) $data['step_8'], 2)); ?></td>
                                        </tr>

                                        
                                        <!--[if BLOCK]><![endif]--><?php if($showWtax): ?>
                                            <tr class="text-muted small fst-italic">
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_1_wtax'], 2)); ?></td>
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_2_wtax'], 2)); ?></td>
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_3_wtax'], 2)); ?></td>
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_4_wtax'], 2)); ?></td>
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_5_wtax'], 2)); ?></td>
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_6_wtax'], 2)); ?></td>
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_7_wtax'], 2)); ?></td>
                                                <td>WTAX: ₱<?php echo e(number_format($data['step_8_wtax'], 2)); ?></td>
                                            </tr>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

            </div>
        </div>
    </div>
</div>


   <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tranchesByYear; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $yearTranches): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card mb-2">
        <div class="card-header bg-primary text-white" data-bs-toggle="collapse" data-bs-target="#year-<?php echo e($year); ?>">
            Year: <?php echo e($year); ?>

        </div>
        <div id="year-<?php echo e($year); ?>" class="collapse">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Name</th>
                            <th>Eligible</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $yearTranches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tranche): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($tranche->name); ?></td>
                                <td><?php echo e($tranche->employmentType->name ?? 'N/A'); ?></td>
                                <td><?php echo e($tranche->is_active ? 'Yes' : 'No'); ?></td>
                                <td>
                                    <button wire:click="view(<?php echo e($tranche->id); ?>)" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="<?php echo e(route('tranches.edit', $tranche->id)); ?>" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <button wire:click="remove(true, <?php echo e($tranche->id); ?>)" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

</div><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/settings/tranches/index.blade.php ENDPATH**/ ?>