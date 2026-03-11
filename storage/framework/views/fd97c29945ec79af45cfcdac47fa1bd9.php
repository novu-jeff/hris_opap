<div> <!-- single root wrapper for Livewire -->
    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2 mb-md-0">
            <label for="cut_off_period" class="form-label mb-0">Cut-off Period</label>
            <select id="cut_off_period" wire:model.change="cutoffPeriod" class="form-select">
                <option value="">All</option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cutOffPeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($period); ?>"><?php echo e($period); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>

        <div class="col-md-4 mb-2 mb-md-0">
            <label class="fw-bold">Filter by Employment Type</label>
            <select wire:model.live="filterEmploymentType" class="form-select">
                <option value="">All</option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $employmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>
    </div>

    <!-- Second row: Show entries & Search input -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-3 mb-2 mb-md-0 d-flex align-items-center gap-2">
            <label for="entries" class="form-label mb-0">Show entries:</label>
            <select id="entries" wire:model.live="entries" class="form-select w-auto">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = [5,10,20,30,40,50,60,70,80,90,100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($num); ?>"><?php echo e($num); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>

        <div class="col-md-4 offset-md-5 mt-2 mt-md-0">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Search Payroll ID or Type">
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Payroll ID</th>
                    <th>Employement Type</th>
                    <th>Cut-off Period</th>
                    <th>Payroll Date</th>
                    <th>No. of Employees</th>  
                    <th>Status</th>               
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($payroll->id); ?></td>
                        <td
                        <?php
                            $typeColors = [
                                'Contractual' => 'bg-primary text-white fw-bold',         // yellow
                                'Contract of Service' => 'bg-info text-white fw-bold',   // blue
                                'Job Order' => 'bg-danger text-white fw-bold',          // red
                            ];

                            $types = explode(', ', $payroll->employment_types ?? '');
                            $classes = collect($types)->map(function($type) use ($typeColors) {
                                return $typeColors[$type] ?? '';
                            })->filter()->implode(' '); // combine classes if multiple types
                        ?>
                        class="<?php echo e($classes); ?>"
                    >
                        <?php echo e($payroll->employment_types ?? '-'); ?>

                    </td>
                        <td><?php echo e($payroll->cut_off_period); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($payroll->payroll_date)->format('M j, Y')); ?></td>
                        <td><?php echo e($payroll->items_count); ?></td>
                        <td>
                        <!--[if BLOCK]><![endif]--><?php if($payroll->status === 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif($payroll->status === 'disapproved'): ?>
                            <span class="badge bg-danger">Disapproved</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Pending</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                        <td>
                            <div class="d-flex gap-2">
                                <!--[if BLOCK]><![endif]--><?php if($payroll->status !== 'pending'): ?>
                                <a target="_blank"
                                    href="<?php echo e(route('reports.payroll.view', $payroll->id)); ?>"
                                    class="btn btn-primary btn-sm"
                                    title="View Payroll">
                                        <i class="fa-regular fa-folder-open"></i>
                                </a>
                                 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <!--[if BLOCK]><![endif]--><?php if($payroll->status !== 'approved'): ?>
                                    <button
                                        wire:click="approve(<?php echo e($payroll->id); ?>)"
                                        class="btn btn-success btn-sm"
                                        title="Approve Payroll">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                
                                <!--[if BLOCK]><![endif]--><?php if($payroll->status !== 'pending'): ?>
                                    <button
                                        wire:click="disapprove(<?php echo e($payroll->id); ?>)"
                                        class="btn btn-warning btn-sm"
                                        title="Pending Payroll">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if($payroll->status !== 'pending'): ?>
                                <button
                                    wire:click="downloadPayroll(<?php echo e($payroll->id); ?>)"
                                    class="btn btn-info btn-sm"
                                    title="Download Payroll"
                                >
                                    <i class="fa-solid fa-download"></i>
                                </button>
                                 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if($payroll->status !== 'approved'): ?>
                                    <button wire:click="remove(true, <?php echo e($payroll->id); ?>)" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center fw-bold py-3">No payroll records found</td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>

        
        <div class="mt-4">
            <?php echo e($payrolls->links()); ?>

        </div>
    </div>
</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/reports/payroll/index.blade.php ENDPATH**/ ?>