<div>
    <div class="clockinout">
        <div class="row">
            <!-- <div class="col-12 col-md-4 mb-5">
                <label for="user" class="mb-3">Manipulate time for testing</label>
                <input type="time" class="form-control" wire:model="manipulate_timestamp">
                <button wire:click="delete" class="btn btn-danger mt-3">Delete Record</button>
            </div> -->
            <div class="col-12 col-md-12 mb-3 mb-3">
                <div class="row capture-content">
                    <div class="col-12 col-md-5">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div data-status="<?php echo e($status); ?>"
                                    class="clock-process card border-3 
                                        <?php echo e(in_array($status, ['Clock In', 'Clock Out']) ? 'border-primary bg-primary text-white' : ''); ?> 
                                        <?php echo e(in_array($status, ['Lunch In', 'Lunch Out']) ? 'border-secondary bg-secondary text-white' : ''); ?> 
                                        <?php echo e($status === 'Done' ? 'border-danger bg-danger text-white' : ''); ?>" 
                                    wire:target="capture">
                                    <div class="card-body d-flex align-items-center">
                                        <div>
                                            <div class="d-flex justify-content-center">
                                                <span wire:loading.remove wire:target="capture">
                                                </span>
                                                <span wire:loading wire:target="capture">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </div>
                                            <div class="text-center fw-bold text-uppercase mt-1">
                                                <span wire:loading.remove wire:target="capture"><?php echo e($status); ?></span>
                                                <span wire:loading wire:target="capture">Saving...</span>
                                            </div>
                                        </div>
                                    </div>      
                                </div>  
                                <!--[if BLOCK]><![endif]--><?php if(in_array($status, ['Lunch Out'])): ?>
                                    <div class="text-center mt-3">
                                        <button style="border-radius: 15px" class="clock-process-forced btn btn-primary border-3 w-100 py-3 text-uppercase fw-bold">
                                            Clock Out
                                        </button>
                                    </div>     
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->       
                            </div>
                            <div class="col-12 mb-3">
                                <div class="card border-3 bg-dark text-white w-100" wire:click="showLogs" wire:target="showLogs">
                                    <div class="card-body d-flex align-items-center">
                                        <div>
                                            <div class="d-flex justify-content-center">
                                                <span wire:loading.remove wire:target="showLogs">
                                                    <i class="fa-regular fa-calendar-check"></i>
                                                </span>
                                                <span wire:loading wire:target="showLogs">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </div>
                                            <div class="text-center fw-bold text-uppercase mt-1">
                                                <span wire:loading.remove wire:target="showLogs">Clock Logs</span>
                                                <span wire:loading wire:target="showLogs">Please Wait...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="camera d-flex justify-content-center align-items-center w-100">
                            <video id="video" autoplay></video>
                            <canvas id="canvas"></canvas>
                            <div class="alert-container">
                                
                            </div>
                            <div class="watermark">
                                <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>">            
                            </div>
                            
                        </div>
                        <div class="text-muted text-center text-muted text-uppercase mt-3 fst-italic">
                            <small>Please ensure your face is clearly visible before proceeding.</small>
                        </div>
                    </div>
                </div>
                <div class="row capture-preview">
                    <div class="col-12">
                        <img src="" alt="" srcset="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="logs_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">
                        <?php echo e(\Carbon\Carbon::now()->format('F Y')); ?>

                    </h1>                    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--[if BLOCK]><![endif]--><?php if(!empty($logs)): ?>
                        <div class="accordion" id="logsAccordion">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $dateKey = str_replace('/', '-', $item['date']);
                                    $logList = $item['logs'] ?? [];
                                    $accomplishment = collect($logList)->firstWhere('accomplishment');
                                    $hasImage = collect($logList)->contains(fn($log) => !empty($log['captured_image']));
                                ?>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo e($dateKey); ?>">
                                        <button class="accordion-button text-uppercase fw-bold <?php echo e($loop->first ? '' : 'collapsed'); ?>"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse<?php echo e($dateKey); ?>"
                                                aria-expanded="<?php echo e($loop->first ? 'true' : 'false'); ?>"
                                                aria-controls="collapse<?php echo e($dateKey); ?>">
                                                <?php echo e(\Carbon\Carbon::createFromFormat('j/n/Y', $item['date'])->format('d, l')); ?>

                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo e($dateKey); ?>"
                                        class="accordion-collapse collapse <?php echo e($loop->first ? 'show' : ''); ?>"
                                        aria-labelledby="heading<?php echo e($dateKey); ?>"
                                        data-bs-parent="#logsAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Clock In</th>
                                                        <th>Lunch Out</th>
                                                        <th>Lunch In</th>
                                                        <th>Clock Out</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <?php echo e(isset($logList[0]['time']) ? \Carbon\Carbon::parse($logList[0]['time'])->format('h:i A') : '-'); ?>

                                                        </td>
                                                        <td>
                                                            <?php echo e(isset($logList[1]['time']) ? \Carbon\Carbon::parse($logList[1]['time'])->format('h:i A') : '-'); ?>

                                                        </td>
                                                        <td>
                                                            <?php echo e(isset($logList[2]['time']) ? \Carbon\Carbon::parse($logList[2]['time'])->format('h:i A') : '-'); ?>

                                                        </td>
                                                        <td>
                                                            <?php echo e(isset($logList[3]['time']) ? \Carbon\Carbon::parse($logList[3]['time'])->format('h:i A') : '-'); ?>

                                                        </td>
                                                    </tr>

                                                    <!--[if BLOCK]><![endif]--><?php if($hasImage): ?>
                                <tr>
                                    <!--[if BLOCK]><![endif]--><?php for($i = 0; $i < 4; $i++): ?>
                                        <td>
                                            <!--[if BLOCK]><![endif]--><?php if(!empty($logList[$i]['captured_image'])): ?>
                                                <?php
                                                    $logImage = 'timelogs/' . $logList[$i]['captured_image'];
                                                ?>
                                                <a style="cursor: pointer" data-fancybox data-src="<?php echo e(Storage::disk('public')->url($logImage)); ?>">
                                                    <img src="<?php echo e(Storage::disk('public')->url($logImage)); ?>"
                                                        alt="log image"
                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                </a>
                                            <?php else: ?>
                                                No Image
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                    <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
                                </tr>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                    <!--[if BLOCK]><![endif]--><?php if(!empty($accomplishment)): ?>
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="text-start mt-2 px-3">
                                                                    <p class="mb-2 fw-bold">Accomplishment Report:</p>
                                            <?php
                                                $accomplishmentFile = $accomplishment['accomplishment'] ?? null;
                                                $accomplishmentPath = $accomplishmentFile ? 'accomplishments/' . $accomplishmentFile : null;
                                                $accomplishmentExists = $accomplishmentPath
                                                    ? Storage::disk('public')->exists($accomplishmentPath)
                                                    : false;
                                            ?>
                                            <!--[if BLOCK]><![endif]--><?php if($accomplishmentExists): ?>
                                                <p class="text-primary d-flex align-items-center gap-2 mt-3">
                                                    <i class="fa-solid fa-download"></i>
                                                    <a href="<?php echo e(Storage::disk('public')->url($accomplishmentPath)); ?>" download>
                                                        <?php echo e($accomplishmentFile); ?>

                                                    </a>
                                                </p>
                                            <?php else: ?>
                                                <p class="text-warning d-flex align-items-center gap-2 mt-3">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                    <span>Accomplishment file not found. Please re-upload.</span>
                                                </p>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                </div>
                                                            </td>    
                                                        </tr>  
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">Currently no clock logs.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>                
            </div>
        </div>
    </div>



    <div class="modal fade" wire:ignore.self id="clockInModal" tabindex="-1" aria-labelledby="clockInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <form wire:submit.prevent="<?php echo e($status === 'Clock Out' || $isForcedOut ? 'saveAccomplishment' : 'triggerClock'); ?>" enctype="multipart/form-data">

                <div class="modal-header border-0 pt-2 pb-0">
                    <h5 class="modal-title text-uppercase fw-bold" id="clockInModalLabel">Captured Image Preview</h5>
                </div>

                <div class="modal-body">
                    
                    <div class="mb-3" wire:ignore>
                        <img id="clockInPreviewImage" src="" alt="Captured Image" class="img-fluid rounded shadow">
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($status === 'Clock Out' || $isForcedOut): ?>
                        
                        <div class="mb-3">
                            <label for="accomplishmentFile" class="text-start">Accomplishment Report</label>
                            <input
                                type="file"
                                wire:model="upload_accomplishment"
                                id="accomplishmentFile"
                                class="form-control"
                                accept="application/pdf"
                            />
                            <small class="text-muted d-block mt-1">PDF only, max 5 MB. Click Proceed right after selecting to avoid upload timeout.</small>

                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['upload_accomplishment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->


                            
                            <!--[if BLOCK]><![endif]--><?php if($upload_accomplishment): ?>
                                <p class="mt-2">
                                    Selected File: 
                                    
                                      <strong><?php echo e($upload_accomplishment->getClientOriginalName()); ?></strong>
                                  
                                </p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div wire:ignore class="modal-footer border-0 d-flex gap-2 justify-content-between align-items-center">
                    <button type="button" class="retakeButton btn btn-danger py-3 px-5 text-uppercase fw-bold" data-bs-dismiss="modal">
                        Retake
                    </button>

                    <button type="submit"
                        class="btn btn-primary py-3 px-5 text-uppercase fw-bold d-flex align-items-center gap-2"
                        wire:target="<?php echo e($status === 'Clock Out' || $isForcedOut ? 'saveAccomplishment' : 'triggerClock'); ?>"
                        wire:loading.attr="disabled">
                        <span>Proceed</span>
                        <span wire:loading wire:target="<?php echo e($status === 'Clock Out' || $isForcedOut ? 'saveAccomplishment' : 'triggerClock'); ?>">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</div>

<script type="module">
    $(function() {
        initializeClockFace();
    })
</script>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/livewire/employee/clock.blade.php ENDPATH**/ ?>