<div>
    <div class="d-md-flex justify-content-end gap-3">
         <button wire:click="selectPayroll('<?php echo e($type); ?>')" class="btn btn-primary text-uppercase px-5 py-3 fw-medium" type="button">
            Generate Payroll
        </button>          
    </div>
    <div class="card border-0 mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
               <?php
                    $reportComponentMap = [
                        'salary'             => 'admin.payroll.reports.salary',
                        'clothing_allowance' => 'admin.payroll.reports.clothing-allowance',
                        'mid_year'           => 'admin.payroll.reports.mid-year',
                        'year_end'           => 'admin.payroll.reports.year-end',
                        'ot_pay'             => 'admin.payroll.reports.ot-pay',
                    ];
                ?>

                <!--[if BLOCK]><![endif]--><?php if(isset($reportComponentMap[$type])): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split($reportComponentMap[$type], [
                        'entries' => $entries,
                        'status' => $status,
                        'type' => $type,
                        'employment_type' => $employment_type
                    ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-628398597-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" data-bs-backdrop="static" id="newPayroll" tabindex="-1" aria-labelledby="newPayrollLabel" aria-hidden="true">
        <div class="modal-dialog <?php echo e($isToCreate ? 'modal-lg' : ''); ?> <?php echo e($activeTab == 'ineligible' ? '' : ''); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase fw-medium fw-bold" id="newPayrollLabel">Create Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <form wire:submit.prevent="createPayroll">
                        <!--[if BLOCK]><![endif]--><?php if(!$isToCreate): ?> 
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $dynamicFormFields['items']['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldKey => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-3">
                                    <!--[if BLOCK]><![endif]--><?php if($field['type'] != 'checkbox'): ?> 
                                        <label for="<?php echo e($fieldKey); ?>" class="form-label text-uppercase">
                                            <?php echo e($field['label']); ?>

                                        </label>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <?php
                                        $inputValue = $field['value'] ?? '';
                                        $inputClass = $field['class'] ?? '';
                                        $inputAttr = $field['attr'] ?? [];
                                    ?>

                                    <!--[if BLOCK]><![endif]--><?php switch($field['type']):
                                        case ('text'): ?>
                                        <?php case ('date'): ?>
                                            <input
                                                type="<?php echo e($field['type']); ?>"
                                                id="<?php echo e($fieldKey); ?>"
                                                class="form-control <?php echo e($inputClass); ?>"
                                                wire:model.defer="<?php echo e($fieldKey); ?>"
                                                value="<?php echo e($inputValue); ?>"
                                                <?php $__currentLoopData = $inputAttr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attrKey => $attrVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e($attrKey); ?>="<?php echo e($attrVal); ?>"
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            >
                                            <?php break; ?>

                                        <?php case ('monthyear'): ?>
                                            <input
                                                type="month"
                                                id="<?php echo e($fieldKey); ?>"
                                                class="form-control <?php echo e($inputClass); ?>"
                                                wire:model.defer="<?php echo e($fieldKey); ?>"
                                                value="<?php echo e(old($fieldKey, $inputValue)); ?>"
                                                <?php $__currentLoopData = $inputAttr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attrKey => $attrVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e($attrKey); ?>="<?php echo e($attrVal); ?>"
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            >
                                            <?php break; ?>

                                        <?php case ('select'): ?>
                                            <select
                                                id="<?php echo e($fieldKey); ?>"
                                                class="form-control <?php echo e($inputClass); ?>"
                                                wire:model.defer="<?php echo e($fieldKey); ?>"
                                                <?php $__currentLoopData = $inputAttr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attrKey => $attrVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e($attrKey); ?>="<?php echo e($attrVal); ?>"
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            >
                                                <option value="">-- CHOOSE --</option>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $field['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($option->id); ?>" 
                                                        <?php echo e((old($fieldKey, $inputValue) == $option->id) ? 'selected' : ''); ?>

                                                    >
                                                        <?php echo e($option->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </select>
                                            <?php break; ?>
                                        <?php case ('checkbox'): ?>
                                            <div class="form-check">
                                                <input 
                                                    type="checkbox" 
                                                    id="<?php echo e($fieldKey); ?>" 
                                                    class="form-check-input <?php echo e($inputClass); ?>" 
                                                    wire:model.defer="<?php echo e($fieldKey); ?>"
                                                    <?php $__currentLoopData = $inputAttr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attrKey => $attrVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($attrKey); ?>="<?php echo e($attrVal); ?>"
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                >
                                                <label class="form-check-label" for="<?php echo e($fieldKey); ?>">
                                                    <?php echo e($field['label'] ?? ucfirst(str_replace('_', ' ', $fieldKey))); ?>

                                                </label>
                                            </div>
                                        <?php break; ?>
                                    <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->

                                    <div class="error-field">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = [$fieldKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                            <div class="d-flex justify-content-end mt-5 pb-2">
                                <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" type="submit" >
                                    <span wire:loading.remove wire:target="createPayroll">Next</span>
                                    <span wire:loading wire:target="createPayroll">
                                        Please Wait <i class="fa-solid fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        <?php else: ?>
                            <h5 class="mt-3 mb-4 text-uppercase fw-bold">Below are the eligible and ineligible for payroll processing</h5>
                            <ul class="nav nav-pills mb-3" id="employeeTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button wire:ignore.self wire:click="setActiveTab('eligible')" class="nav-link text-uppercase fw-bold active" id="eligible-tab" data-bs-toggle="tab" data-bs-target="#eligible" type="button" role="tab" aria-controls="eligible" aria-selected="true">
                                        Eligible
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button wire:ignore.self wire:click="setActiveTab('ineligible')" class="nav-link text-uppercase fw-bold" id="ineligible-tab" data-bs-toggle="tab" data-bs-target="#ineligible" type="button" role="tab" aria-controls="ineligible" aria-selected="false">
                                        Ineligible
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="employeeTabsContent">
                                <div wire:ignore.self class="tab-pane fade show active" id="eligible" role="tabpanel" aria-labelledby="eligible-tab">
                                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                                        <span class="fw-bold text-success text-uppercase fw-bold">Total Eligible: <?php echo e($employeesChecked['eligible']['count']); ?></span>
                                    </div>
                                    <div class="table-responsive mt-2" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-striped table-bordered w-100 m-0">
                                            <thead class="table-light" style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee No</th>
                                                    <th>Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $employeesChecked['eligible']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td><?php echo e($index + 1); ?></td>
                                                        <td><?php echo e($employee['employee_no'] ?? 'N/A'); ?></td>
                                                        <td><?php echo e($employee['name']); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center fw-bold py-3">No data was found</td>
                                                    </tr> 
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div wire:ignore.self class="tab-pane fade" id="ineligible" role="tabpanel" aria-labelledby="ineligible-tab">
                                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                                        <span class="fw-bold text-danger text-uppercase fw-bold">Total Ineligible: <?php echo e($employeesChecked['ineligible']['count']); ?></span>
                                    </div>
                                    <div class="table-responsive mt-2" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-striped table-bordered w-100 m-0">
                                            <thead class="table-light" style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee No</th>
                                                    <th>Name</th>
                                                    <th>Reason</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $employeesChecked['ineligible']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td><?php echo e($index + 1); ?></td>
                                                        <td><?php echo e($employee['employee_no'] ?? 'N/A'); ?></td>
                                                        <td><?php echo e($employee['name']); ?></td>
                                                        <td>
                                                            <div class="text-danger">
                                                                <?php echo e(is_array($employee['reason']) ? implode(', ', $employee['reason']) : $employee['reason']); ?>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center fw-bold py-3">No data was found</td>
                                                    </tr> 
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5 pb-3">
                                <!--<button class="btn btn-outline-primary px-5 py-3 text-uppercase" type="button" wire:click="go_back">
                                    Go Back
                                </button>-->
                                <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" type="submit">
                                    <span wire:loading.remove wire:target="createPayroll">Create</span>
                                    <span wire:loading wire:target="createPayroll">
                                        Creating <i class="fa-solid fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($isBatchProcessing): ?> 
        <div 
            class="modal fade d-block show"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            tabindex="-1"
            aria-modal="true"
            role="dialog"
            style="background-color: rgba(0, 0, 0, 0.5);"
        >
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content text-center py-4 shadow">
                    <div class="modal-body">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        
                        <p class="fw-bold mb-0 text-uppercase text-muted">
                            <?php echo e($batchStatusMessage); ?>

                        </p>

                        <div class="px-4">
                            <div class="progress mb-2 mt-3" style="height: 30px;">
                                <div 
                                    class="progress-bar progress-bar-striped progress-bar-animated bg-primary fw-bold" 
                                    role="progressbar" 
                                    style="width: <?php echo e($batchProgress); ?>%; font-size: 12px;" 
                                    aria-valuenow="<?php echo e($batchProgress); ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <?php echo e($batchProgress); ?>%
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button class="btn btn-danger text-uppercase fw-bold px-4 py-2" wire:click="cancel_payroll" wire:loading.attr="disabled">
                                <i class="fa-solid fa-circle-xmark me-1"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div wire:poll.3000ms="checkBatchStatus"></div>
        </div>    
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>

<?php $__env->startSection('script'); ?>
    <script>
        $(function() {

            Livewire.on('initDateRange', (event) => {
                $('.range').attr('autocomplete', 'off');
                $('.range').daterangepicker({
                    locale: { format: 'YYYY-MM-DD' },
                    autoUpdateInput: false
                });

                $('.range').on('apply.daterangepicker', function(ev, picker) {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('cut_off_period', picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('ot_period', picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
                });
            });

            Livewire.on('start-job-dispatch', (event) => {
                const payroll_id = event[0].payroll_id;
                const employment_type = event[0].employment_type
                const type = event[0].type

                setTimeout(() => {
                    Livewire.dispatch('dispatchPayrollJobs', [payroll_id, employment_type, type]);
                }, 100);
            });

        });
    </script>
<?php $__env->stopSection(); ?><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/payroll/index.blade.php ENDPATH**/ ?>