<div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item d-flex gap-3 my-3" role="presentation">
                    <a href="<?php echo e(route('reports.dtr')); ?>"
                    class="nav-link text-uppercase fw-bold <?php echo e(is_null($selectedType) ? 'active' : ''); ?>">
                        All
                    </a>

                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $employmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employmentType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('reports.dtr', ['type' => $employmentType->id])); ?>"
                        class="nav-link text-uppercase fw-bold <?php echo e($selectedType == $employmentType->id ? 'active' : ''); ?>">
                            <?php echo e($employmentType->name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                    <a href="<?php echo e(route('reports.dtr', ['type' => 'unassigned'])); ?>"
                    class="nav-link text-uppercase fw-bold <?php echo e($selectedType === 'unassigned' ? 'active' : ''); ?>">
                        Unassigned
                    </a>
                </li>
            </ul>
            <div class="row mb-4">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label for="entries" class="form-label mb-0">Show entries:</label>
                    <select id="entries" wire:model.live="entries" class="form-select w-auto">
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
                    <label for="search" class="form-label mb-0">Search:</label>
                    <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Search something...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>Employee No.</th>
                            <th>Name</th>
                            <th style="max-width: 200px;">Action</th>
                        </tr>
                    </thead>                
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($record->employee_no); ?></td>
                                <td>
                                    <?php echo e($record->personal->firstname); ?>

                                    <?php echo e($record->personal->middlename ? substr($record->personal->middlename, 0, 1) . '.' : ''); ?>

                                    <?php echo e($record->personal->lastname); ?>

                                </td>                        
                                <td> 
                                    <a target="_blank" href="<?php echo e(route('dtr.show', ['id' => $record->employee_no])); ?>" class="btn btn-primary">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
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
                <?php echo e($records->links(data: ['scrollTo' => false])); ?>

            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/reports/daily-time-record/index.blade.php ENDPATH**/ ?>