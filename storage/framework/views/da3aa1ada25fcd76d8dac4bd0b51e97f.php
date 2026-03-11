

<!--[if BLOCK]><![endif]--><?php for($copy = 1; $copy <= 2; $copy++): ?>
<div class="dtr-copy">

     <div class="center">
         <!--[if BLOCK]><![endif]--><?php if($product == 'government'): ?>
            <div>
                <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>" style="width: 80px; height: auto;">            
            </div>
        <?php else: ?>
            <div>
                <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>" style="width: 80px; height: auto;"> 
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <div>
            <h5>DAILY TIME RECORD</h1>
            <h5><?php echo e($company); ?></h1>
            <h5>For the month of <div class="underline" style="min-width: auto !important; padding: 0 15px 0 15px !important; text-transform: uppercase"><?php echo e(\Carbon\Carbon::parse($dtrDate)->format('F Y')); ?> </div>(FY)</h1>
        </div>
    </div>

     <table class="info-table">
            <tr><td style="text-align: left"><strong>Name:</strong></td><td style="text-align: left"><?php echo e($logs['employee_account']['firstname'] . ' ' . $logs['employee_account']['middlename'] . ' ' . $logs['employee_account']['lastname']); ?></td></tr>
            <tr><td style="text-align: left"><strong>Position:</strong></td><td style="text-align: left" class="underline"> <?php echo e($logs['employee_account']['position']); ?></td></tr>
             <tr><td style="text-align: left"><strong>Official Time: </strong></td><td style="text-align: left; text-transform: capitalize;" class="underline"><?php echo e($officialTime['shift_duration'] ?? 'Flexible'); ?></td></tr>
            <tr><td style="text-align: left"><strong>Office/Department:</strong></td><td style="text-align: left" class="underline"> <?php echo e($logs['employee_account']['section']); ?></td></tr>
        </table>


    <table class="p-dtr-table">
        <thead>
            <tr>
                <th>Days</th>
                <th colspan="2">AM</th>
                <th colspan="2">PM</th>
                <th colspan="2">OVERTIME</th>
                <th colspan="2">AUT</th>
               <th class="remarks-col">Remark</th>
            </tr>
            <tr>
                <th></th>
                <th>In</th>
                <th>Out</th>
                <th>In</th>
                <th>Out</th>
                <th>Hours</th>
                <th>Mins</th>
                <th>Hours</th>
                <th>Mins</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $logs['dtr']['logs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="position: relative; ">
                        <?php echo e(\Carbon\Carbon::parse($key)->format('d D')); ?>

                        <!--[if BLOCK]><![endif]--><?php if($day['clock_in'] !== null && $day['origin'] === 'web'): ?>
                            <div class="shaded-box">|</div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                    <!-- AM -->
                    <td>
                        <!--[if BLOCK]><![endif]--><?php if(isset($day['clock_in'])): ?>
                            <?php echo e(\Carbon\Carbon::parse($day['clock_in'])->format('g:i A')); ?>

                        <?php else: ?>
                            <?php echo e(' '); ?>

                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                    <td><?php echo e(isset($day['lunch_in']) ? \Carbon\Carbon::parse($day['lunch_in'])->format('g:i A') : ' '); ?></td>

                    <!-- PM -->
                    <td><?php echo e(isset($day['lunch_out']) ? \Carbon\Carbon::parse($day['lunch_out'])->format('g:i A') : ' '); ?></td>
                    <td> <?php echo e(isset($day['clock_out']) ? \Carbon\Carbon::parse($day['clock_out'])->format('g:i A') : ' '); ?></td>

                    <?php
                        // Flag to check if it's a future date
                        $isFuture = $day['isFuture'] ?? false;
                        $isBreakRequired = $day['is_break_required'] ?? false;
                    
                        // Allowed remarks
                        $allowedRemarks = ['absent', 'rest day', 'special hol', 'legal hol'];
                    
                        // Normalize and check remarks
                        $remarks = $day['remarks'] ?? null;
                        $isEmpty = false;
                    
                        // Check if any required clock times are missing or empty
                        $clockIn = $day['clock_in'] ?? null;
                        $lunchIn = $day['lunch_in'] ?? null;
                        $lunchOut = $day['lunch_out'] ?? null;
                        $clockOut = $day['clock_out'] ?? null;
                    
                        // If any clock times are empty or null, mark isEmpty = true

                        if($isBreakRequired){
                            if (empty($clockIn) && empty($lunchIn) && empty($lunchOut) && empty($clockOut)) {
                                $isEmpty = true;
                            }
                        } else {
                            if (empty($clockIn) && empty($clockOut)) {
                                $isEmpty = true;
                            }
                        }

                        // overtime
                        $overtimeMinutes = $day['aut']['overtime']['minutes']  ?? 0;
                        $otHour = floor($overtimeMinutes / 60);
                        $otMins = $overtimeMinutes % 60;
                        
                        // total AUT
                        $tardinessMinutes = $day['aut']['tardiness']['minutes'] ?? 0;
                        $tarHours = floor($tardinessMinutes / 60);
                        $tarMins = $tardinessMinutes % 60;

                        // total AUT
                        $undertimeMinutes = $day['aut']['undertime']['minutes'] ?? 0;
                        $underHours = floor($undertimeMinutes / 60);
                        $underMins = $undertimeMinutes % 60;

                        $autHours = $tarHours + $underHours;
                        $autMins = $tarMins + $underMins;
                    ?>
                
                    
                    <!-- Overtime: Calculate Hours -->
                    <td>
                        <!--[if BLOCK]><![endif]--><?php if(!$isFuture): ?>
                            <!--[if BLOCK]><![endif]--><?php if(!$isEmpty): ?>
                                <?php echo e($otHour); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                    
                    <!-- Overtime: Calculate Minutes -->
                    <td>
                        <!--[if BLOCK]><![endif]--><?php if(!$isFuture): ?>
                            <!--[if BLOCK]><![endif]--><?php if(!$isEmpty): ?>
                                <?php echo e($otMins); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                    
                    <!-- Total AUT Hours -->
                    <td>
                        <!--[if BLOCK]><![endif]--><?php if(!$isFuture): ?>
                            <!--[if BLOCK]><![endif]--><?php if(!$isEmpty): ?>
                                <?php echo e($autHours); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                    
                    <!-- Remaining Minutes -->
                    <td>
                        <!--[if BLOCK]><![endif]--><?php if(!$isFuture): ?>
                            <!--[if BLOCK]><![endif]--><?php if(!$isEmpty): ?>
                                <?php echo e($autMins); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                    
                   <td class="remarks-col">
                        <!--[if BLOCK]><![endif]--><?php if(isset($day['remarks']) && is_array($day['remarks'])): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $day['remarks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $remark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <small><?php echo e($remark); ?></small>
                                <?php 
                                    $nextIndex = $index + 1;
                                    $totalRemarks = count($day['remarks']);
                                ?>
                        
                                <!--[if BLOCK]><![endif]--><?php if($nextIndex < $totalRemarks): ?>
                                    <!--[if BLOCK]><![endif]--><?php if($nextIndex % 2 == 0): ?>
                                        <br> 
                                    <?php else: ?>
                                        <small>, </small>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php else: ?>
                            <small> </small>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($isAdmin): ?>
                            <!--[if BLOCK]><![endif]--><?php if(
                                    isset($day['remarks'])
                                    && (
                                        in_array('Discrepancy', $day['remarks'])
                                        || in_array('Absent', $day['remarks'])
                                    )
                                ): ?>
                                <a href="<?php echo e(route('timekeeping.correction-apply', [
                                    'bsd_no' => $logs['employee_account']['bsd_no'] ?? null,
                                    'date' => \Carbon\Carbon::parse($key)->format('Y-m-d'),
                                ])); ?>" class="btn btn-sm btn-danger btn-correction">
                                    Correction
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->   
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>                      
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </tbody>
    </table>
    <div class="dtr-summary">
        <h5 class="text-center text-uppercase">Total Summary</h5>
        <div class="dtr-summary-container">
            <div class="dtr-summary-item">Days Worked = <?php echo e($logs['dtr']['summary']['worked_days'] ?? '0'); ?></div>
            <div class="dtr-summary-item">Tardiness =
                <?php echo e($logs['dtr']['summary']['tardiness'] ?? '0'); ?>

            </div>
            <div class="dtr-summary-item">Leave = <?php echo e($logs['dtr']['summary']['leaves'] ?? '0'); ?></div>
            <div class="dtr-summary-item">Absences = <?php echo e($logs['dtr']['summary']['absences'] ?? '0'); ?></div>
            <div class="dtr-summary-item">TA Freq. = <?php echo e($logs['dtr']['summary']['tardiness_freq']); ?> </div>
            <div class="dtr-summary-item">Rest Day = <?php echo e($logs['dtr']['summary']['rest_days'] ?? '0'); ?></div>
            <div class="dtr-summary-item">Overtime = <?php echo e($logs['dtr']['summary']['overtime'] ?? '0'); ?></div>
            <div class="dtr-summary-item">Undertime =
                <?php echo e($logs['dtr']['summary']['undertime'] ?? '0'); ?>

            </div>
            <div class="dtr-summary-item">Special Hol. = <?php echo e($logs['dtr']['summary']['special_hol'] ?? '0'); ?></div>
            <div class="dtr-summary-item">Total Days of Work = <?php echo e($logs['dtr']['summary']['total_days_of_work'] ?? '0'); ?></div>
            <div class="dtr-summary-item">UT Freq. = <?php echo e($logs['dtr']['summary']['undertime_freq']); ?></div>
            <div class="dtr-summary-item">Legal Hol. = <?php echo e($logs['dtr']['summary']['legal_hol'] ?? '0'); ?></div>
            <div class="dtr-summary-item">Less TA/UT  = <?php echo e($logs['dtr']['summary']['less_aut'] ?? '0'); ?> </div>
        </div>
    </div>
    <div class="sepe" style="margin-top: 40px;"></div>
    <div class="certify">
        I, CERTIFY on my honor that the above is a true and correct report of the hours of work
        performed, record of which was made daily at the time of arrival and departure from office.
    </div>
    <div class="signature">
        <h5><?php echo e($logs['employee_account']['firstname'] . ' ' . $logs['employee_account']['middlename'] . ' ' . $logs['employee_account']['lastname']); ?></h5>
        <div class="sepe"></div>
        <h6>(Name and Signature of Employee)</h6>
        <div class="sepe" style="margin-top: 30px;"></div>
        <p>Verified as to prescribed office hours (In-Charge)</p>
    </div>
    <div class="remarks">
        <h6 style="text-transform: uppercase">Remarks:</h6>
        <p><?php echo e($logs['remarks'] ?? ''); ?></p>   
    </div>


</div>

<?php endfor; ?><!--[if ENDBLOCK]><![endif]--><?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/admin/reports/daily-time-record/employee/print-dtr-table.blade.php ENDPATH**/ ?>