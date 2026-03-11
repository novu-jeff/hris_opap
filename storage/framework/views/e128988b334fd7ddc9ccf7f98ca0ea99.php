<div class="card border-0 mt-3">
    <div class="card-body p-0">
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
                        <th>Name</th>
                        <th>Date</th>
                        <th style="max-width: 200px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($record->name); ?></td>
                            <td>
                                <?php echo e(\Carbon\Carbon::createFromFormat('m-d', $record->date)->format('F j')); ?>

                            </td>
                            <td>
                                <a href="<?php echo e(route('holiday.edit', ['holiday' => $record->id])); ?>" class="btn btn-primary mx-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button class="btn btn-danger mx-1" wire:click="remove(true, <?php echo e($record->id); ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>                            
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
</div><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/settings/payroll/holiday/index.blade.php ENDPATH**/ ?>