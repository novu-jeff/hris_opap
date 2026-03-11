<div>
    <div class="modal fade" id="alert_employee" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog custom-modal modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Upload Reports</h1>
                    <button type="button" class="btn-close" wire:click="close_upload_employee" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--[if BLOCK]><![endif]--><?php if($resultMessage): ?>
                        <ul class="nav nav-pills d-flex justify-content-center" wire:ignore id="myTab" role="tablist">
                            <?php foreach ($resultMessage as $index => $message): ?>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-uppercase <?= $index === 0 ? 'active' : ''; ?>" id="tab-<?= $index ?>" data-bs-toggle="tab" href="#content-<?= $index ?>" role="tab"><?= $message['section'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <hr>
                        <div class="tab-content" id="myTabContent">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $resultMessage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="tab-pane fade <?php echo e($index === 0 ? 'show active' : ''); ?>" id="content-<?php echo e($index); ?>" role="tabpanel">
                                    <p class="text-uppercase"><strong>Inserted Records: (<?php echo e($message['insertedCount']); ?>)</strong></p>
                                    <ul class="text-uppercase">
                                        <!--[if BLOCK]><![endif]--><?php if(!empty($message['insertedList'])): ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $message['insertedList']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inserted): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <?php echo e($inserted['message']); ?>

                                                    <span class="ms-2">
                                                        <a target="_blank" href="<?php echo e(route('hris.show', ['employee_no' => $inserted['employee_no']])); ?>" class="text-decoration-underline text-primary">View</a>
                                                    </span>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <li>No records inserted</li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>
                                    <p class="text-uppercase"><strong>Updated Records: (<?php echo e($message['updatedCount']); ?>)</strong></p>
                                    <ul class="text-uppercase">
                                        <!--[if BLOCK]><![endif]--><?php if(!empty($message['updatedList'])): ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $message['updatedList']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $updated): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <?php echo e($updated['message']); ?>

                                                    <span class="ms-2">
                                                        <a target="_blank" href="<?php echo e(route('hris.show', ['employee_no' => $updated['employee_no'], 'form' => 'information'])); ?>" class="text-decoration-underline text-primary">View</a>
                                                    </span>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <li>No records updated</li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>                                    
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="upload_employee" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog custom-modal modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Add Employee</h1>
                    <button type="button" class="btn-close" wire:loading.remove wire:target="file" wire:click="close_upload_employee" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button  wire:ignore.self class="nav-link active" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload-add" type="button" role="tab" aria-controls="upload" aria-selected="false">
                                File Upload
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="<?php echo e(route('hris.manual')); ?>" class="nav-link" id="manual-tab" role="tab" aria-controls="manual" aria-selected="true">
                                Manual Adding
                            </a>
                        </li>
                    </ul>
                
                    <hr>
                    <!-- Pills Content -->
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Upload Tab -->
                        <div class="tab-pane fade show active" wire:ignore.self id="upload-add" role="tabpanel" aria-labelledby="upload-tab">
                            <label class="mb-2" for="file">File Upload</label>
                            <input type="file" wire:model="file" id="file" class="form-control" wire:loading.attr="disabled" wire:target="upload_file">
                            <div class="mt-2 text-muted fw-bold text-uppercase d-flex justify-content-between align-items-center" style="font-size: 13px">
                                <small>Note: only files xlsx or xls are allowed.</small>
                                <small><a href="<?php echo e(asset('templates/HRIS-EMPLOYEE-TEMPLATE.xlsx')); ?>" class="nav-link text-decoration-underline">Download Template</a></small>
                            </div>
                            <div wire:loading wire:target="file" class="mt-2 text-center text-muted">
                                <p>Please Wait... <i class="fa-solid fa-spinner fa-spin"></i></p>
                            </div>
                            <div class="mt-3">
                                <!--[if BLOCK]><![endif]--><?php if($upload_preview): ?>
                                    File Ready to import: <a href="<?php echo e($upload_preview); ?>"><?php echo e($upload_preview); ?></a>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div class="form-check my-3">
                                <input class="form-check-input" type="checkbox" wire:change="select_change('linkSchedule')" wire:model="isLinkSchedule" wire:loading.attr="disabled" wire:target="upload_file">
                                <label class="form-check-label">
                                    Link Shift and Schedule
                                </label>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($isLinkSchedule): ?>
                                <hr class="my-4">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <label class="mb-2">Link Shift Schedule</label>
                                        <select wire:model="shift_id" id="shift_id" class="form-select" wire:loading.attr="disabled" wire:target="upload_file">
                                            <option value=""> - CHOOSE - </option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($shift->id); ?>"><?php echo e($shift->name . ' (' . $shift->shift_duration . ')'); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="mb-2">Link Employee Schedule</label>
                                        <select wire:model="schedule_id" id="schedule_id" class="form-select" wire:loading.attr="disabled" wire:target="upload_file">
                                            <option value=""> - CHOOSE - </option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($schedule->id); ?>"><?php echo e($schedule->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                <span class="text-danger"><?php echo e($message); ?></span> 
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php if($upload_preview): ?>
                                <div class="mt-4 d-flex justify-content-end">
                                    <button class="btn btn-primary px-5 py-3 text-uppercase fw-bold" 
                                            wire:click="upload_file"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove>Upload File</span>
                                        <span wire:loading wire:target="upload_file">Importing <i class="fa-solid fa-spinner fa-spin"></i></span>
                                    </button>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->    
                            <div class="w-100 mt-5" wire:loading wire:target="upload_file">
                                <div class="alert alert-danger d-flex justify-content-center gap-3 align-items-center" role="alert">
                                    <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                                    <div class="text-uppercase fw-bold">
                                        Please do not close the modal or refresh the page to prevent errors during the upload process.
                                    </div>
                                </div>  
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-lg-flex justify-content-end text-center mb-5 gap-3">
        <!--[if BLOCK]><![endif]--><?php if(config('app.product') === 'government'): ?>
        <a href="<?php echo e(route('hris.staffing')); ?>" class="btn btn-outline-primary px-5 py-3 text-uppercase mb-3">View Staffing</a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <button class="btn btn-primary px-5 py-3 text-uppercase mb-3" data-bs-toggle="modal" data-bs-target="#upload_employee">Add Employee</button>
    </div>

    <div>
        <div class="row mb-5">
            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                <li class="nav-item d-flex gap-3 my-3" role="presentation">
                    <a href="<?php echo e(route('hris.index')); ?>"
                    class="nav-link text-uppercase fw-bold <?php echo e(is_null($selectedType) ? 'active' : ''); ?>">
                        All
                    </a>
                </li>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $employmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employmentType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="nav-item d-flex gap-3 my-3" role="presentation">
                        <a href="<?php echo e(route('hris.index', ['employment_type' => $employmentType->id])); ?>"
                        class="nav-link text-uppercase fw-bold <?php echo e($selectedType == $employmentType->id ? 'active' : ''); ?>">
                            <?php echo e($employmentType->name); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                <li class="nav-item d-flex gap-3 my-3" role="presentation">
                    <a href="<?php echo e(route('hris.index', ['employment_type' => 'unassigned'])); ?>"
                    class="nav-link text-uppercase fw-bold <?php echo e($selectedType === 'unassigned' ? 'active' : ''); ?>">
                        Unassigned
                    </a>
                </li>
                <li class="nav-item ms-auto d-flex gap-3 my-3" role="presentation">
                    <a href="<?php echo e(route('hris.index', ['employment_type' => 'archived'])); ?>"
                    class="nav-link text-uppercase fw-bold <?php echo e($selectedType === 'archived' ? 'active' : ''); ?>">
                        Archived
                    </a>
                </li>
            </ul>
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
                <input id="search" wire:model.live="search" type="text" class="form-control w-50" placeholder="Employee No. or Name">
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th></th>
                        <th>Employee No</th>
                        <th>Employee Name</th>
                        <th>Date Hired</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  
                        <tr data-id="<?php echo e($item->employee_no); ?>">
                            <td class="text-center">
                                <?php
                                    $fullname = optional($item->personal)->firstname && optional($item->personal)->lastname
                                        ? $item->personal->firstname . ' ' . $item->personal->lastname
                                        : null;

                                    // Profile path
                                    $profile = $item->personal->profile ?? null; 
                                    //dd($profile);
                                    $hasProfile = $profile && Storage::disk('public')->exists($profile);   
                                ?>

                                <!--[if BLOCK]><![endif]--><?php if(!$item->isTransferingEmp): ?>
                                     <!--[if BLOCK]><![endif]--><?php if($hasProfile): ?>
                                        <img src="<?php echo e(asset('storage/' . $profile)); ?>"
                                            alt="Profile"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                    <?php else: ?>
                                        <img src="https://ui-avatars.com/api/?background=005668&color=ffffff&bold=true&name=<?php echo e(urlencode($fullname ?: 'Unknown')); ?>"
                                            style="width: 50px; height: 50px; border-radius: 50%;">
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Loading...</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td><?php echo e($item->employee_no); ?></td>
                            <td>
                                <!--[if BLOCK]><![endif]--><?php if(!$item->isTransferingEmp): ?>
                                    <?php echo $fullname ?? '<span class="text-muted fst-italic">No Name</span>'; ?>

                                <?php else: ?>
                                    <span class="text-muted fst-italic">Loading...</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td>
                                <!--[if BLOCK]><![endif]--><?php if(!$item->isTransferingEmp): ?>
                                    <?php echo e(format_date($item->date_hired, 'day_date_string')); ?>

                                <?php else: ?>
                                    <span class="text-muted fst-italic">Loading...</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td wire:ignore.self>
                                <div class="d-flex gap-2">
                                    <!--[if BLOCK]><![endif]--><?php if($selectedType == 'archived'): ?>
                                        <button wire:click="restore('true', '<?php echo e($item->employee_no); ?>')" class="btn btn-info"
                                            title="Restore Archived Employee">
                                            <i class="fa-solid fa-retweet"></i>
                                        </button>
                                    <?php else: ?>
                                        <a target="_blank" href="<?php echo e(route('download.view', ['show' => 'employee', 'employee_no' => $item->employee_no])); ?>" class="btn btn-primary"
                                            title="Download PDS">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                        <a target="_blank" href="<?php echo e(route('hris.show', ['employee_no' => $item->employee_no, 'form' => 'information'])); ?>" class="btn btn-primary"
                                            title="View Employee Records">
                                            <i class="fa-regular fa-folder-open"></i>
                                        </a>
                                        <a target="_blank" href="<?php echo e(route('dtr.show', ['id' => $item->employee_no])); ?>" class="btn btn-info"
                                            title="View DTR">
                                            <i class="fa-solid fa-business-time"></i>
                                        </a>
                                        <a href="javascript:void(0)" wire:click="changeEmployeeNo('<?php echo e($item->employee_no); ?>')" class="btn btn-info"
                                            title="Change Employee No.">
                                            <i class="fa-solid fa-person-walking-arrow-loop-left"></i>
                                        </a>
                                        <!--[if BLOCK]><![endif]--><?php if(optional($item->account)->isLocked): ?>
                                            <button wire:click="unlock('true', '<?php echo e($item->employee_no); ?>')" class="btn btn-info"
                                                title="Unlock Employee Account">
                                                <i class="fa-solid fa-lock-open"></i>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <button wire:click="remove('true', '<?php echo e($item->employee_no); ?>')" class="btn btn-danger"
                                            title="Remove Employee">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
            <div class="mt-4">
                <?php echo e($employees->links(data: ['scrollTo' => false])); ?>

            </div>
        </div>     
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.hris.change-employee-no');

$__html = app('livewire')->mount($__name, $__params, 'lw-698228654-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/hris/index.blade.php ENDPATH**/ ?>