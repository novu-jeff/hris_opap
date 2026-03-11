<div>
    <div class="row mb-3 mt-5">
        <div class="col-md-6 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.change="entries" class="form-select w-auto">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
            <label for="search" class="form-label mb-0">Filter Status:</label>
            <select wire:model.change="status" id="status" class="form-select w-50">
                <option value=""> - ALL - </option>
                <option value="pending"> Pending </option>
                <option value="approved"> Approved </option>
                <option value="disapproved"> Disapproved </option>
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cut Off Period</th>
                    <th>Payroll Date</th>
                   <th>Has Deductions</th>
                    <th>Status</th>
                    <th style="max-width: 200px;">Action</th>
                </tr>
            </thead>                
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $salary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr data-id="<?php echo e($record->id); ?>">
                        <td>#<?php echo e(format_id($record->id, 6)); ?></td>
                        <td>
                            <?php
                                $dates = explode(' to ', $record->cut_off_period);
                                $startDate = \Carbon\Carbon::parse($dates[0])->format('F d, Y');
                                $endDate = \Carbon\Carbon::parse($dates[1])->format('F d, Y');
                            ?>

                            <?php echo e($startDate); ?> - <?php echo e($endDate); ?>

                        </td>
                        <td><?php echo e(\Carbon\Carbon::parse($record->payroll_date)->format('F d, Y')); ?></td>
                        <td>
                            <div class="alert <?php echo e($record->hasDeductions ? 'alert-danger' : 'alert-primary'); ?> mb-0 py-2 px-3 text-uppercase fw-bold text-center">
                                <?php echo e($record->hasDeductions ? 'yes' : 'no'); ?>

                            </div>
                        </td>
                        <td> <div class="alert <?php echo e($record->status === 'approved' ? 'alert-success' : 'alert-danger'); ?> 
        mb-0 py-2 px-3 text-uppercase fw-bold text-center">
        <?php echo e($record->status); ?>

                            </div></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?php echo e(route('payroll.process', ['type' => $type, 'payroll_id' => $record->id])); ?>" title="View Payroll" class="btn btn-primary">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <!--[if BLOCK]><![endif]--><?php if($record->status !== 'approved'): ?>
                                    <button class="btn btn-info" title="Regenerate Payroll"
                                        wire:click="regeneratePayroll('<?php echo e($record->id); ?>')">
                                        <i class="fa-solid fa-arrows-rotate fa-spin"></i>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <button class="btn btn-danger" title="Delete Payroll" wire:click="removePayroll('true', '<?php echo e($record->id); ?>')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                    </tr> 
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <?php echo e($salary->links(data: ['scrollTo' => false])); ?>

    </div>
</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/payroll/reports/salary.blade.php ENDPATH**/ ?>