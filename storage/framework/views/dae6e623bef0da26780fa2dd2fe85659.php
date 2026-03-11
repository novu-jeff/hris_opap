<div>
    <div class="modal fade" wire:ignore.self id="showModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">View Leave Application</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="employee_no">Employee No.</label>
                            <input type="text" id="employee_no" class="form-control restricted"
                                value="<?php echo e($view_records->employee->employee_no ?? ''); ?>" readonly>
                        </div>

                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="employee_name">Employee Name</label>
                            <input type="text" id="employee_name" class="form-control restricted"
                                value="<?php echo e($view_records->employee->personal->firstname ?? ''); ?> <?php echo e($view_records->employee->personal->lastname ?? ''); ?>" readonly>
                        </div>

                        <div class="col-12 col-md-12 mb-4">
                            <label class="mb-2" for="date_applied">Date Applied</label>
                            <input type="text" id="date_applied" class="form-control restricted"
                                value="<?php echo e(format_date($view_records->created_at ?? '', 'day_date_time_string')); ?>" readonly>
                        </div>

                        <div class="col-12 mb-4"><hr></div>

                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="type">Leave Type</label>
                            <input type="text" id="type" class="form-control restricted"
                                value="<?php echo e($view_records->leave_type->name ?? ''); ?>" readonly>
                        </div>

                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="duration">Duration</label>
                            <input type="text" id="duration" class="form-control restricted"
                                value="<?php echo e(count($view_records->dates ?? [])); ?> <?php echo e(count($view_records->dates ?? []) === 1 ? 'Day' : 'Days'); ?> - <?php echo e(str_replace('_', ' ', $view_records->duration ?? '')); ?>" readonly>
                        </div>

                       <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="leave_dates">
                                Leave Date<?php echo e(count($view_records->dates ?? []) > 1 ? 's' : ''); ?>

                            </label>
                            <ul style="font-size: 16px;">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $view_records->dates ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <?php echo e(format_date($date->date ?? '', 'day_date_string')); ?>

                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </ul>
                        </div>

                       <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="leave_dates">
                                Leave Equivalent
                            </label>
                            <input type="text" id="duration" class="form-control restricted"
                                value="<?php echo e($view_records->leave_equivalent ?? ''); ?>" readonly>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if(($view_records->leave_type->id ?? null) == 1): ?>
                            <div class="col-12 mb-4">
                                <label class="mb-2" for="location">Location</label>
                                <input type="text" id="location" class="form-control restricted"
                                    value="<?php echo e(($view_records->location_specific ?? '') . ', ' . (($view_records->location ?? '') == 'ph' ? 'Philippines' : 'Abroad')); ?>"
                                    readonly>
                            </div>
                        <?php elseif(($view_records->leave_type->id ?? null) == 2): ?>
                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2" for="patient_type">Patient Type</label>
                                <input type="text" id="patient_type" class="form-control restricted"
                                    value="<?php echo e($view_records->confinement ?? ''); ?>" readonly>
                            </div>

                            <div class="col-12 col-md-6 mb-4">
                                <label class="mb-2" for="illness">Illness</label>
                                <input type="text" id="illness" class="form-control restricted"
                                    value="<?php echo e($view_records->illness ?? ''); ?>" readonly>
                            </div>
                        <?php elseif(($view_records->leave_type->id ?? null) == 8): ?>
                            <div class="col-12 mb-4">
                                <label class="mb-2" for="purpose">Purpose</label>
                                <input type="text" id="purpose" class="form-control restricted"
                                    value="<?php echo e(($view_records->study ?? '') === 'others' ? ($view_records->study_other_purpose ?? '') : ($view_records->study ?? '')); ?>" readonly>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                </div>
                <!--[if BLOCK]><![endif]--><?php if(isset($view_records->status) && $view_records->status === 'pending'): ?>
                    <div class="modal-footer">
                        <button wire:click="disapproved" class="btn btn-danger text-uppercase fw-medium">Disapprove</button>
                        <button wire:click="approved" class="btn btn-primary text-uppercase fw-medium">Approve</button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
    
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('ess.leave', ['status' => 'pending'])); ?>" class="nav-link text-uppercase fw-medium <?php echo e($status === 'pending' ? 'active' : ''); ?>"  role="tab" aria-controls="pills-home" aria-selected="true">Pending</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('ess.leave', ['status' => 'granted'])); ?>" class="nav-link text-uppercase fw-medium <?php echo e($status === 'granted' ? 'active' : ''); ?>" role="tab" aria-controls="pills-profile" aria-selected="false">Granted</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('ess.leave', ['status' => 'disapproved'])); ?>" class="nav-link text-uppercase fw-medium <?php echo e($status === 'disapproved' ? 'active' : ''); ?>" role="tab" aria-controls="pills-profile" aria-selected="false">Disapproved</a>
                </li>
            </ul>
            <div class="tab-content mt-5" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
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
                                    <th>Employee No</th>
                                    <th>Employee Name</th>
                                    <th>Leave Type</th>
                                    <th>Days Covered</th>
                                    <th>Date Applied</th>
                                    <th style="max-width: 200px;">Action</th>
                                </tr>
                            </thead>                
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr data-id="<?php echo e($record->id); ?>">
                                        <td><?php echo e($record->employee_no); ?></td>
                                        <td><?php echo e($record->employee->personal->firstname . ' ' . $record->employee->personal->lastname); ?></td>
                                        <td><?php echo e($record->leave_type->name); ?></td>
                                        <td><?php echo e(count($record->dates)); ?> <?php echo e(count($record->dates) === 1 ? 'Day' : 'Days'); ?></td>
                                        <td><?php echo e(format_date($record->created_at, 'date_string')); ?></td>
                                        <td>
                                            <button type="button" wire:click="view(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button wire:click="remove(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
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
            </div>
        </div>
    </div>  
</div>

<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/ess/leave/index.blade.php ENDPATH**/ ?>