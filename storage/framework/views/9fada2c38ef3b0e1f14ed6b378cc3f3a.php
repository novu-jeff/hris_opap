<div class="main-content flex-grow-1 p-4" >
   
        
        <div class="d-lg-flex justify-content-between align-items-center">
            <div class="section-title">
                <h1>Dashboard</h1>
            </div>
        </div>
        <div class="row mt-5">
            <div class="row">
                <div class="col-12 col-md-7">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4">
                                <h5 class="my-2 text-uppercase fw-bold">Employees</h5>
                            </div>
                            <div class="card-body px-3">
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">
                                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $stats['employee']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $types): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="swiper-slide text-uppercase bg-info p-3 rounded-3 text-white">
                                                <p class="mb-0 fw-bold"><?php echo e($types['employment_type']); ?></p>
                                                <hr>
                                                <h1><?php echo e($types['employee_count']); ?></h1>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <div class="w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                                No employment types to show
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if(count($stats['employee']) > 3): ?> 
                                    <div class="float-end">
                                        <small class="text-uppercase text-muted fw-bold d-flex gap-2 align-items-center">
                                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                            Swipe left or right to view more
                                        </small>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4 d-flex justify-content-between">
                                <h5 class="my-2 text-uppercase fw-bold">Clock In & Out</h5>
                                <h5 class="my-2 text-uppercase fw-bold"><?php echo e(\Carbon\Carbon::now()->format('F d, Y')); ?></h5>
                            </div>
                            <div class="card-body px-3 d-flex">
                                <div class="d-lg-flex gap-3 w-100">
                                    <div class="mb-3 w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Clocked In</p>
                                        <hr>
                                        <h1><?php echo e($stats['clockinout']['clockin']); ?></h1>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">In Progress</p>
                                        <hr>
                                        <h1><?php echo e($stats['clockinout']['inprogress']); ?></h1>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-info p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Clocked Out</p>
                                        <hr>
                                        <h1><?php echo e($stats['clockinout']['clockout']); ?></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4">
                                <h5 class="my-2 text-uppercase fw-bold">Leave Applications</h5>
                            </div>
                            <div class="card-body px-3 d-flex">
                                <div class="d-lg-flex gap-3 w-100">
                                    <div class="mb-3 w-100 text-uppercase bg-secondary p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Pending</p>
                                        <hr>
                                        <h1><?php echo e($stats['leave']['pending']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.leave', ['status' => 'pending'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-success p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Granted</p>
                                        <hr>
                                        <h1><?php echo e($stats['leave']['granted']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.leave', ['status' => 'granted'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-danger p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Disapproved</p>
                                        <hr>
                                        <h1><?php echo e($stats['leave']['rejected']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.leave', ['status' => 'disapproved'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4">
                                <h5 class="my-2 text-uppercase fw-bold">Official Business Applications</h5>
                            </div>
                            <div class="card-body px-3 d-flex">
                                <div class="d-lg-flex gap-3 w-100">
                                    <div class="mb-3 w-100 text-uppercase bg-secondary p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Pending</p>
                                        <hr>
                                        <h1><?php echo e($stats['obs']['pending']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.obs', ['status' => 'pending'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-success p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Granted</p>
                                        <hr>
                                        <h1><?php echo e($stats['obs']['granted']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.obs', ['status' => 'granted'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-danger p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Disapproved</p>
                                        <hr>
                                        <h1><?php echo e($stats['obs']['rejected']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.obs', ['status' => 'disapproved'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white px-4">
                                <h5 class="my-2 text-uppercase fw-bold">Authority To Render Overtime Applications</h5>
                            </div>
                            <div class="card-body px-3 d-flex">
                                <div class="d-lg-flex gap-3 w-100">
                                    <div class="mb-3 w-100 text-uppercase bg-secondary p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Pending</p>
                                        <hr>
                                        <h1><?php echo e($stats['atro']['pending']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.atro', ['status' => 'pending'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-success p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Granted</p>
                                        <hr>
                                        <h1><?php echo e($stats['atro']['granted']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.atro', ['status' => 'granted'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                    <div class="mb-3 w-100 text-uppercase bg-danger p-3 rounded-3 text-white">
                                        <p class="mb-0 fw-bold">Disapproved</p>
                                        <hr>
                                        <h1><?php echo e($stats['atro']['rejected']); ?></h1>
                                        <div class="float-end">
                                            <a href="<?php echo e(route('ess.atro', ['status' => 'disapproved'])); ?>" class="text-white">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-12 col-md-5">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white px-4">
                                    <h5 class="my-2 text-uppercase fw-bold">Recruitment</h5>
                                </div>
                                <div class="card-body px-4">
                                    <div class="row">
                                        <div class="col-12 mb-3 col-md-6">
                                            <a href="<?php echo e(route('job.applicants.index', ['status' => 'pending'])); ?>" class="nav-link">
                                                <div class="mb-0 alert alert-info w-100 text-uppercase fw-bold">Pending: <?php echo e($stats['recruitment']['pending']); ?></div>
                                            </a>
                                        </div>
                                        <div class="col-12 mb-3 col-md-6">
                                            <a href="<?php echo e(route('job.applicants.index', ['status' => 'interview'])); ?>" class="nav-link">
                                                <div class="mb-0 alert alert-warning w-100 text-uppercase fw-bold">Interview: <?php echo e($stats['recruitment']['interview']); ?></div>
                                            </a>
                                        </div>
                                        <div class="col-12 mb-3 col-md-6">
                                            <a href="<?php echo e(route('job.applicants.index', ['status' => 'placement'])); ?>" class="nav-link">
                                                <div class="mb-0 alert alert-secondary w-100 text-uppercase fw-bold">Placement: <?php echo e($stats['recruitment']['placement']); ?></div>
                                            </a>
                                        </div>
                                        <div class="col-12 mb-3 col-md-6">
                                            <a href="<?php echo e(route('job.applicants.index', ['status' => 'onboarding'])); ?>" class="nav-link">
                                                <div class="mb-0 alert alert-primary w-100 text-uppercase fw-bold">Onboarding: <?php echo e($stats['recruitment']['onboarding']); ?></div>
                                            </a>
                                        </div>
                                        <div class="col-12 mb-3 col-md-6">
                                            <a href="<?php echo e(route('job.applicants.index', ['status' => 'hired'])); ?>" class="nav-link">
                                                <div class="mb-0 alert alert-success w-100 text-uppercase fw-bold">Hired: <?php echo e($stats['recruitment']['hired']); ?></div>
                                            </a>
                                        </div>
                                        <div class="col-12 mb-3 col-md-6">
                                            <a href="<?php echo e(route('job.applicants.index', ['status' => 'rejected'])); ?>" class="nav-link">
                                                <div class="mb-0 alert alert-danger w-100 text-uppercase fw-bold">Rejected: <?php echo e($stats['recruitment']['rejected']); ?></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <small class="text-uppercase text-muted fw-bold d-flex gap-2 align-items-center">
                                            Click the recruitement status above to view more
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white px-4">
                                    <h5 class="my-2 text-uppercase fw-bold">Payroll Summary</h5>
                                </div>
                                <div class="card-body px-4">
                                    <div class="row">
                                        <div class="col-12 mb-3 col-md-6">
                                                <div class="mb-0 alert alert-info w-100 text-uppercase fw-bold">Approved: <?php echo e($stats['payroll']['approved']); ?></div>
                                           
                                        </div>
                                        <div class="col-12 mb-3 col-md-6">
                                                <div class="mb-0 alert alert-warning w-100 text-uppercase fw-bold">Pending: <?php echo e($stats['payroll']['pending']); ?></div>
                                         
                                        </div>
                                        
                                        
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white px-4 d-flex justify-content-between">
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($stats['social_security']['billing_month'])): ?>
                                        <h5 class="my-2 text-uppercase fw-bold">
                                            LATEST <?php echo e(env('APP_PRODUCT') == 'government' ? 'GSIS' : 'SSS'); ?> BILLING 
                                        </h5>
                                        <h5 class="my-2 text-uppercase fw-bold">
                                            (<?php echo e($stats['social_security']['billing_month']); ?>)
                                        </h5>
                                    <?php else: ?>
                                        <h5 class="my-2 text-uppercase fw-bold">
                                            LATEST <?php echo e(env('APP_PRODUCT') == 'government' ? 'GSIS' : 'SSS'); ?> BILLING
                                        </h5>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="card-body">
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($stats['social_security']['items']) && count($stats['social_security']['items']) > 0): ?>
                                        <table class="table text-uppercase fw-bold w-100 data-tables">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>BP No</th>
                                                    <th>CRN No</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $stats['social_security']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($billing['bp_no'] ?? 'N/A'); ?></td>
                                                        <td><?php echo e($billing['crn_no'] ?? 'N/A'); ?></td> 
                                                        <td>₱<?php echo e(number_format($billing['ps'], 2) ?? 'N/A'); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <small class="text-uppercase text-muted">No <?php echo e(env('APP_PRODUCT') == 'government' ? 'GSIS' : 'SSS'); ?> Billing Found.</small>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white px-4">
                                    <h5 class="my-2 text-uppercase fw-bold">Other Earnings</h5>
                                </div>
                                <div class="card-body px-4 d-flex">
                                    <!--[if BLOCK]><![endif]--><?php if(count($stats['earnings']) > 0): ?>
                                        <ul class="text-uppercase fw-bold list-unstyled">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $stats['earnings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earnings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li style="font-size: 12px"><?php echo e($earnings['name']); ?> <i class="fa fa-check text-primary fs-6 ms-1" aria-hidden="true"></i></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </ul>
                                    <?php else: ?>
                                        <small class="text-uppercase text-muted">No Earnings Found.</small>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white px-4">
                                    <h5 class="my-2 text-uppercase fw-bold">Other Deductions</h5>
                                </div>
                                <div class="card-body px-4 d-flex">
                                    <!--[if BLOCK]><![endif]--><?php if(count($stats['deductions']) > 0): ?>
                                        <ul class="text-uppercase fw-bold list-unstyled">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $stats['deductions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deductions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li style="font-size: 12px"><?php echo e($deductions['name']); ?> <i class="fa fa-check text-primary fs-6 ms-1" aria-hidden="true"></i></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </ul>
                                    <?php else: ?>
                                        <small class="text-uppercase text-muted">No Deductions Found.</small>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                        <div class="card trail">
                            <div class="card-header bg-primary text-white px-4 d-flex justify-content-between">
                                <div>
                                    <h5 class="my-2 text-uppercase fw-bold">Audit Trail Logs</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="scrollable">
                                    <div class="px-4 pt-3">
                                        <!--[if BLOCK]><![endif]--><?php if(!empty($this->trails)): ?>
                                            <ul class="list-unstyled">
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->trails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <a href="javascript:void(0)" wire:click="download('<?php echo e($log); ?>')" class="d-flex align-items-center gap-2"><i class="fa-solid fa-download"></i> <?php echo e($log); ?></a>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </ul>
                                        <?php else: ?> 
                                            <p class="text-muted fw-bold text-uppercase text-center">no trails found</p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>       
            </div>
        </div>
   
</div><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/dashboard/index.blade.php ENDPATH**/ ?>