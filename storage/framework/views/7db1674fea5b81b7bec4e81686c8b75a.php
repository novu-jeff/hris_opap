<div class="card border-0 mt-3">
    <div class="card-body p-0">
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
                <select wire:model.change="status" id="status" class="form-select w-50 text-uppercase">
                    <option value="all">All</option>
                    <option value="pending"> Pending </option>
                    <option value="granted"> Granted </option>
                    <option value="disapproved"> Disapproved </option>
                    <option value="cancelled"> Cancelled </option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Clock Range</th>
                        <!--[if BLOCK]><![endif]--><?php if($status == 'all'): ?>
                            <th>Status</th>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <th style="max-width: 200px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
               
                        <tr data-id="<?php echo e($record->id); ?>">
                            <td>#<?php echo e(format_id($record->id, 6)); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($record->date)->format('F d, Y')); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($record->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($record->end_time)->format('g:i A')); ?></td>
                            <td>
                                <?php echo status_alert($record->status); ?>    
                            </td>  
                            <td>
                                <!--[if BLOCK]><![endif]--><?php if($record->status == 'cancelled'): ?>
                                     <button wire:click="remove(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1" title="Delete File">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if($record->status == 'mentioned'): ?>
                                    <a href="javascript:void(0)" wire:click="download(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1" title="Download file">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if($record->status == 'pending'): ?>
                                    <a href="javascript:void(0)" wire:click="download(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1" title="Download file">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <a href="<?php echo e(route('employee.atro.edit', ['id' => $record->id])); ?>" class="btn btn-primary mx-1" title="Edit file">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button wire:click="cancel(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if($record->status == 'approved'): ?>
                                     <a href="javascript:void(0)" wire:click="download(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1" title="Download File">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <button wire:click="remove(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1" title="Delete File">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->      
                                <!--[if BLOCK]><![endif]--><?php if($record->status == 'disapproved'): ?>
                                    <button wire:click="remove(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1" title="Delete File">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                     <a href="<?php echo e(route('employee.atro.edit', ['id' => $record->id])); ?>" class="btn btn-primary mx-1" title="View File">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="12" class="text-center fw-bold py-3">No data was found</td>
                        </tr> 
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
            <div class="mt-4">
                <?php echo e($records->links(data: ['scrollTo' => false])); ?>

            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/atro/index.blade.php ENDPATH**/ ?>