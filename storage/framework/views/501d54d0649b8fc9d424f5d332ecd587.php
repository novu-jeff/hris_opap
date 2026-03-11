<div>
    <div class="modal fade" wire:ignore.self id="showModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">View ATRO Application</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="employee_no">Employee No.</label>
                            <input type="text" id="employee_no" class="form-control restricted" value="<?php echo e(isset($view_records) ? ($view_records->employee_no) : ''); ?>" readonly>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="employee_name">Employee Name</label>
                            <input type="text" id="employee_name" class="form-control restricted" value="<?php echo e(isset($view_records) ? $view_records->employee->personal->firstname . ' ' . $view_records->employee->personal->lastname : ''); ?>" readonly>
                        </div>
                        <div class="col-12 col-md-4 mb-4">
                            <label class="mb-2" for="date">Date</label>
                            <input type="date" id="date" class="form-control restricted" value="<?php echo e(isset($view_records) ? $view_records->date : ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="start_time">Start Time</label>
                            <input type="text" id="start_time" class="form-control restricted" 
                                   value="<?php echo e(isset($view_records) ? format_time($view_records->start_time ): ''); ?>" 
                                   readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <label class="mb-2" for="end_time">End Time</label>
                            <input type="text" id="end_time" class="form-control restricted" 
                                   value="<?php echo e(isset($view_records) ? format_time($view_records->end_time) : ''); ?>" 
                                   readonly>
                        </div>
                    </div>    
                
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="justification">Justification</label>
                            <textarea id="justification" class="form-control restricted" rows="5" readonly><?php echo e(isset($view_records) ? $view_records->justification : ''); ?></textarea>                            
                        </div>
                    </div>
                   <!--[if BLOCK]><![endif]--><?php if(isset($view_records->status) && $view_records->status === 'disapproved'): ?>   
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="disapproval_note">Reason for Disapproval</label>
                            <textarea id="disapproval_note" class="form-control restricted" rows="3"  readonly><?php echo e(isset($view_records) ? $view_records->disapproval_note : ''); ?></textarea>
                        </div>
                    </div>
                     <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                     <?php if(isset($view_records->status) && $view_records->status === 'pending'): ?>   
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="disapproval_note">Reason for Disapproval</label>
                            <textarea id="disapproval_note" class="form-control" rows="3" wire:model.defer="disapproval_note" ></textarea>
                        </div>
                    </div>
                     <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                
                <?php if(isset($view_records->status) && $view_records->status === 'pending'): ?>
                    <div class="modal-footer">
                        <button wire:click="disapproved" class="btn btn-danger text-uppercase fw-medium">Disapprove</button>
                        <button wire:click="approved" class="btn btn-primary text-uppercase fw-medium">Approve</button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
    <div class="modal fade" wire:ignore.self id="showOffices" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Choose Office</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select wire:model.live="officeSelected" class="form-select">
                        <option value=""> - CHOOSE - </option>
                        <!--[if BLOCK]><![endif]--><?php if($offices): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($office->id); ?>"><?php echo e($office->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($officeSelected): ?>
                    <div class="modal-footer">
                        <button wire:click="download" class="btn btn-primary text-uppercase fw-medium">Download</button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary fw-bold text-uppercase px-4 py-3">HRMS-PD Form 05</button>
            <button type="button" class="btn btn-outline-primary fw-bold px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a class="dropdown-item" wire:click="showOffices('<?php echo e(\Carbon\Carbon::parse($date)->format('Y-m-d')); ?>')" href="javascript:void(0)"><?php echo e(\Carbon\Carbon::parse($date)->format('F d, Y')); ?></a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                <hr class="mb-2 mt-2">
                <li>
                    <a href="javascript:void(0)" wire:change="showOffices($event.target.value)" class="dropdown-item text-center text-uppercase fw-bold">
                        <input type="date" class="form-control">
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('ess.atro', ['status' => 'pending'])); ?>" class="nav-link text-uppercase fw-medium <?php echo e($status === 'pending' ? 'active' : ''); ?>"  role="tab" aria-controls="pills-home" aria-selected="true">Pending</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('ess.atro', ['status' => 'granted'])); ?>" class="nav-link text-uppercase fw-medium <?php echo e($status === 'granted' ? 'active' : ''); ?>" role="tab" aria-controls="pills-profile" aria-selected="false">Granted</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="<?php echo e(route('ess.atro', ['status' => 'disapproved'])); ?>" class="nav-link text-uppercase fw-medium <?php echo e($status === 'disapproved' ? 'active' : ''); ?>" role="tab" aria-controls="pills-profile" aria-selected="false">Disapproved</a>
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
                                    <th>Employee NO.</th>
                                    <th>Employee Name</th>
                                    <th>Date Applied</th>
                                    <th style="max-width: 200px;">Action</th>
                                </tr>
                            </thead>                
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr data-id="<?php echo e($record->id); ?>">
                                        <td><?php echo e($record->employee_no); ?></td>
                                        <td><?php echo e($record->employee->personal->firstname . ' ' . $record->employee->personal->lastname); ?></td>
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
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/ess/atro/index.blade.php ENDPATH**/ ?>