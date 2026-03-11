<div>
    <div class="action mb-4">
        
    </div>
    <hr class="mt-0">
    <div class="text-uppercase fw-bold">
        <!--[if BLOCK]><![endif]--><?php if($isApproved): ?>
            <h2 class="text-success fw-bold text-uppercase text-center">Approved</h2>
        <?php else: ?>
            <h2 class="text-danger fw-bold text-uppercase text-center">Pending</h2>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    <hr>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                Type : <span class="ms-2"><?php echo e($records['payroll']['type']); ?></span>
            </div>
            <div class="text-uppercase fw-bold">
                Date : <span class="ms-2"><?php echo e($records['payroll']['formatted_payroll_date']); ?></span>
            </div>
            <div class="text-uppercase fw-bold">
                Cut-off Period : <span class="ms-2"><?php echo e($records['payroll']['formatted_cutoff_period']); ?></span>
            </div>
            <div class="text-uppercase fw-bold">
                Employee Type : <span class="ms-2"><?php echo e($records['payroll']['formatted_employment_type']); ?></span>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="text-uppercase fw-bold">
                No. of employees : <span class="ms-2"><?php echo e($records['payroll']['no_employees']); ?></span>
            </div>
            <div class="text-uppercase fw-bold">
                Net Amount : <span class="ms-2">PHP <?php echo e(number_format($records['payroll']['overall_net_amount'], 2)); ?></span>
            </div>
            <div class="text-uppercase fw-bold">
                Salary Amount : <span class="ms-2">PHP <?php echo e(number_format($records['payroll']['overall_salary'], 2)); ?></span>
            </div>
        </div>
    </div>
    <hr class="pt-3">
    <?php
        $status = $records['payroll']['status'];
    ?>
    <!--[if BLOCK]><![endif]--><?php if($product == 'government'): ?>
        <!--[if BLOCK]><![endif]--><?php if($records['payroll']['employment_type']['id'] == '1'): ?>
            <div class="table-responsive pb-3 ">
                <table class="table-striped payroll-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="vertical-text text-dark">Status</th>
                            <th rowspan="2">No.</th>
                            <th rowspan="2" class="text-center">Name</th>
                            <th rowspan="2" class="text-center">Position</th>
                            <th rowspan="2" class="text-center">Basic Salary</th>
                            <th rowspan="2" class="text-center">Pera</th>
                            <th rowspan="2" class="text-center">Gross Amount Earned</th>
                            <th colspan="29" class="text-center">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                  
                            <th colspan="10" class="text-center"></th>
                            <th colspan="10" class="text-center">Salary</th> 
                        </tr>
                        <tr>
                            <th colspan="2" class="vertical-text green">RLIP</th>
                            <th colspan="2" class="vertical-text yellow">HDMF</th>
                            <th colspan="2" class="vertical-text skyblue">PHILHEALTH</th>
                            <th colspan="2" class="vertical-text green">CONSOLOAN</th>
                            <th colspan="2" class="vertical-text green">EMERGYLN</th>
                            <th colspan="2" class="vertical-text green">PLREG</th>
                            <th colspan="2" class="vertical-text green">MPL</th>
                            <th colspan="2" class="vertical-text green">MPL LITE</th>
                            <th colspan="2" class="vertical-text green">CPL</th>
                            <th colspan="2" class="vertical-text yellow">MP2</th>
                            <th colspan="2" class="vertical-text yellow">MPL STLMS</th>
                            <th colspan="2" class="vertical-text yellow">CIR375, CIR449</th>
                            <th colspan="2" class="vertical-text red">W/TAX</th>
                            <th colspan="2" class="vertical-text red">UCA</th>
                            <th class="vertical-text grey">AUT</th>
                            <th class="text-center">TOTAL DED.</th>
                            <th class="text-center">NET AMOUNT</th>
                            <th colspan="2" class="vertical-text grey">DBP BRANCH</th>
                            <th colspan="2" class="vertical-text grey">KAWANI</th>
                            <th colspan="4" class="vertical-text grey">LBP PAYROLL ACCOUNT</th>
                            <th colspan="4" class="text-center">1st Half</th>
                            <th colspan="4" class="text-center">2nd Half</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records['payroll_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionIndex => $sectionGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="fw-bold bg-primary text-white sticky-top" style="top: 55px; z-index: 9;">
                                <td colspan="100%">
                                    <div class="d-flex justify-content-between w-100 px-5">
                                        <span><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                        <span class="text-center flex-grow-1"><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                        <span><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                    </div>
                                </td>
                            </tr>

                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sectionGroup['employees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employeeIndex => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="marked-changed">
                                            <!--[if BLOCK]><![endif]--><?php if(in_array($record['id'], $updatedItems)): ?>
                                                <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-check ready" title="No changes made"></i>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td>
                                        #<?php echo e($employeeIndex + 1); ?>

                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information'])); ?>"
                                        class="text-dark" target="_blank">
                                            <?php echo e($record['name']); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($record['position']); ?></td>
                                    <td><?php echo e(number_format($record['basic_salary'], 2)); ?></td>
                                    <td><?php echo e(number_format($record['pera'], 2)); ?></td>
                                    <td><?php echo e(number_format($record['gross_amount_earned'], 2)); ?></td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="rlip.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="hdmf.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="philhealth.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>    
                                        
                                      </td>
                                    <td colspan="2">
                                             <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="consoloan.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>    
                                    </td>
                                    <td colspan="2">
                                           <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="emergency_loan.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                    </td>
                                    <td colspan="2">
                                             <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="plreg.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                          
                                    </td>
                                    <td colspan="2">
                                             <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mpl.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                          
                                        </td>
                                    <td colspan="2">
                                             <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mpl_lite.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                          
                                        </td>    
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="cpl.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                          
                                        </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mp2.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                    </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mplstlms.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?>" style="width: 120px;" <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                        </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="cir375_cir449.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                        </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="w_tax.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                        </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="uca.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td>
                                         <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="aut.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                        
                                        </td>
                                    
                                    <td>
                                         <input type="text"
                                            wire:model.lazy="total_deductions.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'total_deductions')"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                        
                                       </td>
                                    <td>
                                        <input type="text" class="form-control wide-input <?php echo e($isApproved ? 'restricted' : ''); ?>" " 
                                            wire:model.lazy="net_amount.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>" 
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'net_amount')"
                                        <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                        </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="dbp.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" style="width: 120px;" <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="kawani.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="4">
                                          <input type="text" class="form-control wide-input <?php echo e($isApproved ? 'restricted' : ''); ?>" 
                                         wire:model.lazy="lbp_payroll_account.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>" 
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'lbp_payroll_account')"
                                        <?php echo e($isApproved ? 'readonly' : ''); ?>> 
                                        </td>
                                    <td colspan="4">

                                        <input type="text"
                                        class="form-control wide-input
                                            <?php echo e(($isApproved || $isSecondCutoff) ? 'restricted' : ''); ?>"
                                        wire:model.lazy="net_first_half.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                        wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'net_first_half')"
                                        <?php echo e(($isApproved || $isSecondCutoff) ? 'readonly' : ''); ?>

                                    >

                                   
                                    </td>
                                    <td colspan="4">
                                          <input type="text"
                                            class="form-control wide-input
                                                <?php echo e(($isApproved) ? 'restricted' : ''); ?>"
                                            wire:model.lazy="net_second_half.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'net_second_half')"
                                            <?php echo e(($isApproved ) ? 'readonly' : ''); ?>

                                        >

                                       
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="12" class="py-3 text-uppercase fw-bold text-muted">
                                    No data found
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($records['payroll']['employment_type']['id'] == '2'): ?>
            <div class="table-responsive pb-3">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2" class="vertical-text text-dark">Status</th>
                            <th rowspan="2">No.</th>
                            <th rowspan="2" class="text-center">Name</th>
                            <th rowspan="2" class="text-center">Position</th>
                            <th rowspan="2" class="text-center">Basic Salary</th>
                            <th colspan="20" class="text-center">DEDUCTIONS: (GSIS, MPL, PHILHEALTH, AUT, and W/TAX)</th>
                  
                            <th colspan="10" class="text-center"></th>
                            <th colspan="10" class="text-center">Salary</th> 
                        </tr>
                        <tr>
                            <th colspan="2" class="vertical-text yellow">HDMF</th>
                            <th colspan="2" class="vertical-text skyblue">PHIL HEALTH</th>
                            <th colspan="2" class="vertical-text green">MPL</th>
                            <th colspan="2" class="vertical-text green">MPL LITE</th>
                            <th colspan="2" class="vertical-text yellow">MP2</th>
                            <th colspan="2" class="vertical-text yellow">MPL STLMS</th>
                            <th colspan="2" class="vertical-text yellow">CIR375, CIR449</th>
                            <th class="vertical-text grey">AUT</th>
                            <th colspan="2" class="vertical-text red">UCA</th>
                            <th colspan="2" class="vertical-text red">W/TAX</th>
                            <th class="text-center">TOTAL DED.</th>
                            <th class="text-center">NET AMOUNT</th>
                            <th colspan="2" class="vertical-text grey">DBP</th>
                            <th colspan="2" class="vertical-text grey">KAWANI</th>
                            <th colspan="4" class="vertical-text grey">LBP PAYROLL ACCOUNT</th>
                            <th colspan="4" class="text-center">1st Half</th>
                            <th colspan="4" class="text-center">2nd Half</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records['payroll_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionIndex => $sectionGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="fw-bold bg-primary text-white sticky-top" style="top: 55px; z-index: 9;">
                                <td colspan="100%">
                                    <div class="d-flex justify-content-between w-100 px-5">
                                        <span><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                        <span class="text-center flex-grow-1"><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                        <span><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                    </div>
                                </td>
                            </tr>

                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sectionGroup['employees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employeeIndex => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="marked-changed">
                                            <!--[if BLOCK]><![endif]--><?php if(in_array($record['id'], $updatedItems)): ?>
                                                <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-check ready" title="No changes made"></i>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td>
                                        #<?php echo e($employeeIndex + 1); ?>

                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information'])); ?>"
                                        class="text-dark" target="_blank">
                                            <?php echo e($record['name']); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($record['position']); ?></td>
                                    <td><?php echo e(number_format($record['basic_salary'], 2)); ?></td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="hdmf.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="philhealth.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>    
                                        
                                      </td>
                                    
                                    <td colspan="2">
                                             <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mpl.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                          
                                        </td>
                                    <td colspan="2">
                                             <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mpl_lite.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                          
                                        </td>   
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mp2.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                    </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="mplstlms.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?>" style="width: 120px;" <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                        </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="cir375_cir449.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                        </td>
                                    <td>
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                        wire:model="aut.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                        class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    
                                    </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="uca.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="2">
                                        <input type="number" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="w_tax.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>  
                                        
                                        </td>
                                    
                                    
                                    
                                    <td>
                                         <input type="text"
                                            wire:model.lazy="total_deductions.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'total_deductions')"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                        
                                       </td>
                                    <td>
                                        <input type="text" class="form-control wide-input <?php echo e($isApproved ? 'restricted' : ''); ?>" " 
                                            wire:model.lazy="net_amount.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>" 
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'net_amount')"
                                        <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                        </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="dbp.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input" style="width: 120px;" <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" wire:change="recompute(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>)"
                                            wire:model="kawani.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            class="form-control <?php echo e($isApproved ? 'restricted' : ''); ?> wide-input"  <?php echo e($isApproved ? 'readonly' : ''); ?>>
                                    </td>
                                    <td colspan="4">
                                          <input type="text" class="form-control wide-input <?php echo e($isApproved ? 'restricted' : ''); ?>" 
                                         wire:model.lazy="lbp_payroll_account.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>" 
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'lbp_payroll_account')"
                                        <?php echo e($isApproved ? 'readonly' : ''); ?>> 
                                        </td>
                                    <td colspan="4">

                                        <input type="text"
                                        class="form-control wide-input
                                            <?php echo e(($isApproved || $isSecondCutoff) ? 'restricted' : ''); ?>"
                                        wire:model.lazy="net_first_half.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                        wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'net_first_half')"
                                        <?php echo e(($isApproved || $isSecondCutoff) ? 'readonly' : ''); ?>

                                    >

                                    </td>
                                    <td colspan="4">
                                          <input type="text"
                                            class="form-control wide-input
                                                <?php echo e(($isApproved || $isFirstCutoff) ? 'restricted' : ''); ?>"
                                            wire:model.lazy="net_second_half.<?php echo e($sectionIndex); ?>.<?php echo e($employeeIndex); ?>"
                                            wire:keydown="manualEdit(<?php echo e($sectionIndex); ?>, <?php echo e($employeeIndex); ?>, 'net_second_half')"
                                            <?php echo e(($isApproved || $isFirstCutoff) ? 'readonly' : ''); ?>

                                        >

                                       
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="12" class="py-3 text-uppercase fw-bold text-muted">
                                    No data found
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>


        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    
    <!--[if BLOCK]><![endif]--><?php if($product == 'private'): ?>
    <div class="table-responsive pb-3">
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Basic Salary</th>
                    <th>Overtime</th>
                    <th>Holiday Pay</th>
                    <th>Allowances</th>
                    <th>Gross Amount</th>
                    <th>SSS</th>
                    <th>PhilHealth</th>
                    <th>Pagibig</th>
                    <th>W/Tax</th>
                    <th>AUT</th>
                    <th>Other Loans</th>
                    <th>Total Deductions</th>
                    <th>Net Amount</th>
                    <th>Bank Name</th>
                    <th>Bank Account</th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $records['payroll_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionIndex => $sectionGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="fw-bold bg-primary text-white sticky-top" style="top: 55px; z-index: 9;">
                        <td colspan="100%">
                            <div class="d-flex justify-content-between w-100 px-5">
                                <span><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                <span class="text-center flex-grow-1"><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                                <span><?php echo e($sectionGroup['section_name'] ?? 'Unknown Section'); ?></span>
                            </div>
                        </td>
                    </tr>

                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sectionGroup['employees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employeeIndex => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="marked-changed">
                                    <!--[if BLOCK]><![endif]--><?php if(in_array($record['id'] ?? null, $updatedItems ?? [])): ?>
                                        <i class="fa-solid fa-triangle-exclamation unsaved" title="Unsaved changes"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-check ready" title="No changes made"></i>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                            <td>#<?php echo e($employeeIndex + 1); ?></td>
                            <td>
                                <a href="<?php echo e(route('hris.show', ['employee_no' => $record['employee_no'], 'form' => 'information'])); ?>"
                                class="text-dark" target="_blank">
                                    <?php echo e($record['name']); ?>

                                </a>
                            </td>
                            <td><?php echo e($record['position']); ?></td>
                            <td><?php echo e(number_format($record['basic_salary'], 2)); ?></td>
                            <td><?php echo e(number_format($record['overtime_pay'], 2)); ?></td>
                            <td><?php echo e(number_format($record['holiday_pay'], 2)); ?></td>
                            <td><?php echo e(number_format($record['allowances'], 2)); ?></td>
                            <td><?php echo e(number_format($record['gross_amount_earned'], 2)); ?></td>
                            <td><?php echo e(number_format($record['sss'], 2)); ?></td>
                            <td><?php echo e(number_format($record['philhealth'], 2)); ?></td>
                            <td><?php echo e(number_format($record['pagibig'], 2)); ?></td>
                            <td><?php echo e(number_format($record['w_tax'], decimals: 2)); ?></td>
                            <td><?php echo e(number_format($record['aut'], decimals: 2)); ?></td>
                            <td><?php echo e(number_format($record['other_loans'], 2)); ?></td>
                            <td><?php echo e(number_format($record['total_deductions'], 2)); ?></td>
                            <td><?php echo e(number_format($record['net_amount'], 2)); ?></td>
                            <td>
                                <?php echo e($record['bank_name']); ?>

                            </td>
                            <td>
                                <?php echo e($record['bank_account']); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="20" class="py-3 text-uppercase fw-bold text-muted">
                            No payroll data found.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($hasChanges): ?>
        <div class="d-flex justify-content-end mt-5">
            <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="save">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">
                    Saving <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if(!$isApproved && !$hasChanges): ?>
        <div class="d-flex justify-content-end mt-5">
            <button class="btn btn-primary px-5 py-3 text-uppercase" wire:loading.attr="disabled" wire:click="approve">
                <span wire:loading.remove wire:target="approve">Approve</span>
                <span wire:loading wire:target="approve">
                    Please Wait <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>


<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/payroll/process/salary.blade.php ENDPATH**/ ?>