<div class="card border-0 mt-3">

    <!-- Applications Cards -->
    <div class="applications mb-3">
        <div class="row g-3">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-3">
                    <a href="<?php echo e(route($application['route'])); ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100" style="cursor:pointer;">
                            <div class="card-body text-center p-3">
                                <h3 class="fw-bold mb-1"><?php echo e($application['count']); ?></h3>
                                <small class="text-uppercase fw-semibold"><?php echo e($application['title']); ?></small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs px-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#leaveapplication">Leave Application</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#history">Leave Card History</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#credits">Leave Credits</a>
        </li>
    </ul>

    <div class="tab-content p-3">

        <!-- TAB 1: LEAVE APPLICATION -->
        <div class="tab-pane fade show active" id="leaveapplication">

            <div class="card-body p-0">
                <div class="row mb-3 mt-3">
                    <div class="col-md-6 d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Show entries:</label>
                        <select wire:model.live="entries" class="form-select w-auto">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = [5,10,20,30,40,50,60,70,80,90,100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($num); ?>"><?php echo e($num); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>

                    <div class="col-md-6 d-flex justify-content-end align-items-center gap-2">
                        <label class="form-label mb-0">Filter Status:</label>
                        <select wire:model.change="status" class="form-select w-50 text-uppercase">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="granted">Granted</option>
                            <option value="disapproved">Disapproved</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Date</th>
                                <!--[if BLOCK]><![endif]--><?php if($status == 'all'): ?>
                                    <th>Status</th>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <th style="max-width: 200px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>#<?php echo e(format_id($record->id, 6)); ?></td>
                                    <td><?php echo e('(' . $record->leave_type->code . ') - ' . $record->leave_type->name); ?></td>

                                    <td>
                                        <?php echo e(\Carbon\Carbon::parse($record->from)->format('F d, Y')); ?>

                                        <!--[if BLOCK]><![endif]--><?php if($record->to): ?>
                                            - <?php echo e(\Carbon\Carbon::parse($record->to)->format('F d, Y')); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>

                                    <!--[if BLOCK]><![endif]--><?php if($status == 'all'): ?>
                                        <td><?php echo status_alert($record->status); ?></td>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($record->status == 'cancelled'): ?>
                                            <button wire:click="remove(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($record->status == 'pending'): ?>
                                            <a href="<?php echo e(route('employee.leave.edit', ['id' => $record->id])); ?>" class="btn btn-primary mx-1">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            <a href="javascript:void(0)" wire:click="download(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                <i class="fa-solid fa-download"></i>
                                            </a>

                                            <button wire:click="cancel(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($record->status == 'granted'): ?>
                                            <a href="javascript:void(0)" wire:click="download(<?php echo e($record->id); ?>)" class="btn btn-primary mx-1">
                                                <i class="fa-solid fa-download"></i>
                                            </a>

                                            <button wire:click="remove(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($record->status == 'disapproved'): ?>
                                            <button wire:click="remove(true, <?php echo e($record->id); ?>)" class="btn btn-danger mx-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
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
        </div>

        <!-- TAB 2: LEAVE CARD HISTORY -->
        <div class="tab-pane fade" id="history">
            <h5 class="fw-bold mb-3">Leave Card History</h5>

            <div>
    <form wire:submit.prevent="save">
        <div class="table-responsive mt-5">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">Period</th>
                        <th rowspan="2">Particulars</th>
                        <th colspan="4">Vacation Leave</th>
                        <th colspan="4">Sick Leave</th>
                        <th rowspan="2">Remarks</th>
                    </tr>
                    <tr>
                        <th>EARNED</th>
                        <th>AUT w/ pay</th>
                        <th>BAL.</th>
                        <th>AUT w/o pay</th>
                        <th>EARNED</th>
                        <th>AUT w/ pay</th>
                        <th>BAL.</th>
                        <th>AUT w/o pay</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $leaverecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="year-header" data-year="<?php echo e($year); ?>" style="cursor: pointer; background-color: #f8f9fa;">
                            <td colspan="1"><?php echo e($year); ?></td>
                            <td colspan="3" style="font-weight: 500 !important">
                                <?php echo $total_bal[$year]['vl'] != 0 || $total_bal[$year]['sl'] != 0 ? '(Bal. brought forward from ' . ($year - 1) . ')' : ''; ?>

                            </td>
                            <td colspan="4">
                                <strong><?php echo e($total_bal[$year]['vl'] != 0 ? $total_bal[$year]['vl'] : ''); ?></strong>
                            </td>
                            <td colspan="12">
                                <strong><?php echo e($total_bal[$year]['sl'] != 0 ? $total_bal[$year]['sl'] : ''); ?></strong>
                            </td>
                        </tr>
                        <?php 
                            $latestYear = collect($leaverecords)->keys()->max();
                        ?>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $data['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="year-content" wire:ignore.self data-year="<?php echo e($year); ?>" style="<?php echo e($year == $latestYear ? '' : 'display: none;'); ?>">
                                <td><?php echo e($item['period']); ?></td>
                                <td>
                                    <textarea wire:model="particulars.<?php echo e($year); ?>.<?php echo e($key); ?>" wire:key="particulars-<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 400px; height: 100px;"></textarea>
                                </td>
                                <td>
                                    <input type="text" wire:key="vl_earned-<?php echo e($year); ?>.<?php echo e($key); ?>" wire:change="onChange('vl', <?php echo e($year); ?>, <?php echo e($key); ?>)" wire:model="vl_earned.<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 100px;" value="<?php echo e($item['vl_earned'] ?? ''); ?>">
                                </td>
                                <td style="color: red">
                                    <input type="text" wire:key="vl_aut_w_pay-<?php echo e($year); ?>.<?php echo e($key); ?>" wire:change="onChange('vl', <?php echo e($year); ?>, <?php echo e($key); ?>)" wire:model="vl_aut_w_pay.<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 100px;" value="<?php echo e($item['vl_aut_w_pay'] ?? ''); ?>">
                                </td>
                                <td style="color: red"><?php echo e($vl_bal[$year][$key]); ?></td>
                                <td>
                                    <input type="text" wire:key="vl_aut_wo_pay-<?php echo e($year); ?>.<?php echo e($key); ?>" wire:model="vl_aut_wo_pay.<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 100px;" value="<?php echo e($item['vl_aut_wo_pay'] ?? ''); ?>">
                                </td>
                                <td>
                                    <input type="text" wire:key="sl_earned-<?php echo e($year); ?>.<?php echo e($key); ?>" wire:change="onChange('sl', <?php echo e($year); ?>, <?php echo e($key); ?>)" wire:model="sl_earned.<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 100px;" value="<?php echo e($item['sl_earned'] ?? ''); ?>">
                                </td>
                                <td style="color: red">
                                    <input type="text" wire:key="sl_aut_w_pay-<?php echo e($year); ?>.<?php echo e($key); ?>" wire:change="onChange('sl', <?php echo e($year); ?>, <?php echo e($key); ?>)" wire:model="sl_aut_w_pay.<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 100px;" value="<?php echo e($item['sl_aut_w_pay'] ?? ''); ?>">
                                </td>
                                <td style="color: red"><?php echo e($sl_bal[$year][$key]); ?></td>
                                <td>
                                    <input type="text" wire:key="sl_aut_wo_pay-<?php echo e($year); ?>.<?php echo e($key); ?>" wire:model="sl_aut_wo_pay.<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 100px;" value="<?php echo e($item['sl_aut_wo_pay'] ?? ''); ?>">
                                </td>
                                <td>
                                    <textarea wire:model="remarks.<?php echo e($year); ?>.<?php echo e($key); ?>" wire:key="remarks-<?php echo e($year); ?>.<?php echo e($key); ?>" class="form-control restricted" readonly style="width: 200px; height: 80px;"></textarea>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="12" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
    </form>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 20px 8px 20px;
            vertical-align: middle;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        .year-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left !important;
            color: red;
        }
    </style>
</div>



        </div>

        <!-- TAB 3: LEAVE CREDITS -->
        <div class="tab-pane fade" id="credits">
            <div>
                <div class="row">

            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $leaveBalances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   

                    <div class="col-12 col-md-4 mb-3">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0 text-uppercase fw-bold py-3"><?php echo e($leave['name']); ?> (<?php echo e($leave['code']); ?>)</h5>
                            </div>
                            <div class="card-body">
                                <h1> <?php echo e($leave['balance']); ?> days</h1>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>  
        </div>

    </div>

</div>

<script>
    $(function () {
        $(".year-header").click(function () {
            let year = $(this).data("year");

            $(".year-content").not("[data-year='" + year + "']").hide();

            $(".year-content[data-year='" + year + "']").toggle();
        });
    });
</script>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/leave/index.blade.php ENDPATH**/ ?>