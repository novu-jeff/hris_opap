<div class="employee-sidebar" id="adminSidebar">

    <div class="sidebar-header">
        <img src="<?php echo e(asset('/img/' . $provider['client_logo'])); ?>" class="sidebar-logo">
        <h4 class="company-name"><?php echo e($companyInfo->name ?? 'Admin Panel'); ?></h4>
    </div>

    <div class="sidebar-menu">

        <!-- Dashboard -->
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="menu-item">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>

        <!-- Recruitment -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
            'read jobs',
            'read applicants'
        ])): ?>
        <div class="menu-group">
            <p class="menu-group-title"><i class="fa-solid fa-user-plus"></i> Recruitment</p>
            <div class="submenu-items">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read jobs')): ?>
                <a href="<?php echo e(route('job.posts.index')); ?>" class="submenu-item">
                    <i class="fa-solid fa-briefcase"></i> Job Posting
                </a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read applicants')): ?>
                <a href="<?php echo e(route('job.applicants.index', ['status' => 'pending'])); ?>" class="submenu-item">
                    <i class="fa-solid fa-user-check"></i> Applicants
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- HRIS -->
        <a href="<?php echo e(route('hris.index')); ?>" class="menu-item">
            <i class="fa-solid fa-users"></i> HRIS
        </a>

        <!-- Timekeeping -->
        <?php if(config('app.allow_upload_timelogs')): ?>
        <div class="menu-group">
            <p class="menu-group-title"><i class="fa-solid fa-clock"></i> Timekeeping</p>
            <div class="submenu-items">
                 <?php if(config('app.allow_upload_timelogs')): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('write timelogs')): ?>
                        <a href="<?php echo e(route('timekeeping.upload')); ?>" class="submenu-item">
                            <i class="fa-solid fa-upload"></i>  Add Time Logs
                        </a>
                    <?php endif; ?>
                 <?php endif; ?>   
            </div>
        </div>
        <?php endif; ?>

        <!-- Payroll -->
        <a href="<?php echo e(route('payroll.index')); ?>" class="menu-item">
            <i class="fa-solid fa-dollar-sign"></i> Payroll
        </a>

         <!-- ESS -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
            'read leave',
            'read obs',
            'read atro',
            'read announcements',
            'read employee-profile-update',
            'read request-status'
        ])): ?>
        <div class="menu-group">
            <p class="menu-group-title"><i class="fa-solid fa-user-cog"></i> Employee Self Service</p>
            <div class="submenu-items">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read leave')): ?>
                <a href="<?php echo e(route('ess.leave')); ?>" class="submenu-item">
                    <i class="fa-solid fa-calendar-minus"></i> Leave Applications
                </a>
                <?php endif; ?>

               

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read obs')): ?>
                <a href="<?php echo e(route('ess.obs')); ?>" class="submenu-item">
                    <i class="fa-solid fa-briefcase"></i> Official Business Slip Application
                </a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read atro')): ?>
                <a href="<?php echo e(route('ess.atro')); ?>" class="submenu-item">
                    <i class="fa-solid fa-clock"></i> Authority To Render Overtime Application</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read time-adjustments')): ?>
                <a href="<?php echo e(route('ess.time-adjustments')); ?>" class="submenu-item">
                    <i class="fa-solid fa-clock-rotate-left"></i> Time Adjustments</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read payslip-request')): ?>
                <a href="<?php echo e(route('ess.payslip-request')); ?>" class="submenu-item">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Payslip Request</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read announcements')): ?>
                <a href="<?php echo e(route('ess.announcements.index')); ?>" class="submenu-item">
                    <i class="fa-solid fa-bullhorn"></i> Announcements</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read employee-profile-approval')): ?>
                <a href="<?php echo e(route('ess.approval-profile.index')); ?>" class="submenu-item">
                    <i class="fa-solid fa-user-check"></i> Employee Profile Approval</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read messages')): ?>
                <a href="<?php echo e(route('ess.messages')); ?>" class="submenu-item">
                    <i class="fa-solid fa-envelope"></i> Messages</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read faqs')): ?>
                <a href="<?php echo e(route('ess.faqs.index')); ?>" class="submenu-item">
                     <i class="fa-solid fa-question-circle"></i> FAQs</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
            'read dtr',
            'read bir-2316'
        ])): ?>
        <div class="menu-group">
            <p class="menu-group-title"><i class="fa-solid fa-chart-line"></i> Reports</p>
            <div class="submenu-items">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read dtr')): ?>
                <a href="<?php echo e(route('reports.dtr')); ?>" class="submenu-item">
                    <i class="fa-solid fa-clipboard-list"></i> Daily Time Record</a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read dtr')): ?>
                <a href="<?php echo e(route('reports.payroll')); ?>" class="submenu-item">
                    <i class="fa-solid fa-clipboard-list"></i> Payroll Record</a>
                <?php endif; ?>
                <?php if($product == 'private'): ?>
                    <a href="<?php echo e(route('reports.bir')); ?>" class="submenu-item">
                        <i class="fa-solid fa-file-lines"></i> BIR</a>
                    <a href="<?php echo e(route('reports.philhealth')); ?>" class="submenu-item">
                        <i class="fa-solid fa-heart-circle-check"></i> PhilHeath</a>
                    <a href="<?php echo e(route('reports.sss')); ?>" class="submenu-item">
                        <i class="fa-solid fa-id-card"></i> SSS</a>
                    <a href="<?php echo e(route('reports.pagibig')); ?>" class="submenu-item">
                        <i class="fa-solid fa-hand-holding-heart"></i> Pagibig</a>
                
                 <?php endif; ?>
            </div>    
        </div>
         <?php endif; ?> 

         <!-- HRIS -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
            'read company-information', 'read scheduler', 'read tranches', 'read holidays',
            'read branches', 'read departments', 'read sections', 'read assessments', 'read requirements',
            'read users', 'read roles', 'read bank-information', 'read employment-type', 'read positions',
            'read violations', 'read leave-types', 'read gsis-billing', 'read other-earnings', 'read other-deductions'
        ])): ?>
        <!-- Settings -->
        <div class="menu-group">
            <p class="menu-group-title"><i class="fa-solid fa-cogs"></i> Settings</p>

            <div class="submenu-items">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read company-information')): ?>
                <a href="<?php echo e(route('company.index')); ?>" class="submenu-item">
                    <i class="fa-solid fa-building"></i> Company Information</a>
                 <?php endif; ?>
                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read scheduler')): ?>
                    <a class="submenu-item" href="<?php echo e(route('scheduler.index')); ?>">
                         <i class="fa-solid fa-calendar"></i> Scheduler</a>
                <?php endif; ?>
                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read scheduler')): ?>
                    <a class="submenu-item" href="<?php echo e(route('tranches.index')); ?>">
                        <i class="fa-solid fa-layer-group"></i> Tranches</a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read holidays')): ?>
                    <a class="submenu-item" href="<?php echo e(route('holiday.index')); ?>">
                         <i class="fa-solid fa-umbrella-beach"></i> Holiday</a>
                <?php endif; ?>

                <!-- Location Management -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['read branches', 'read departments', 'read sections'])): ?>
                <div class="submenu-subgroup">
                    <p class="submenu-subtitle"> <i class="fa-solid fa-map-pin"></i> Location Management</p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read branches')): ?>
                    <a href="<?php echo e(route('branch.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-building-flag"></i> Central / Field Office</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read departments')): ?>
                    <a href="<?php echo e(route('department.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-network-wired"></i> Clusters</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read sections')): ?>
                    <a href="<?php echo e(route('section.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-sitemap"></i> Sections</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Recruitment -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['read assessments', 'read requirements'])): ?>
                <div class="submenu-subgroup">
                    <p class="submenu-subtitle"> <i class="fa-solid fa-users-gear"></i> Recruitment</p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read assessments')): ?>
                    <a href="<?php echo e(route('job.interview.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-clipboard-question"></i> Assessment</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read requirements')): ?>
                    <a href="<?php echo e(route('job.requirements.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-file-circle-check"></i> Requirements</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- User Management -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['read users', 'read roles'])): ?>
                <div class="submenu-subgroup">
                    <p class="submenu-subtitle">
                        <i class="fa-solid fa-user-gear"></i> User Management</p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read users')): ?>
                    <a href="<?php echo e(route('users.index', ['type' => 'applicants'])); ?>" class="submenu-item">
                        <i class="fa-solid fa-users"></i> Users</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read users')): ?>
                    <a href="<?php echo e(route('user.trails')); ?>" class="submenu-item">
                        <i class="fa-solid fa-clock-rotate-left"></i> Users Audit Trail Logs
                    </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read roles')): ?>
                    <a href="<?php echo e(route('users.access.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-shield-halved"></i> Roles</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- HRIS -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
                    'read bank-information', 'read employment-type', 'read positions',
                    'read violations', 'read leave-types', 'read gsis-billing',
                    'read other-earnings', 'read other-deductions'
                ])): ?>
                <div class="submenu-subgroup">
                    <p class="submenu-subtitle"><i class="fa-solid fa-id-card-clip"></i> HRIS</p>

                   
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read employment-type')): ?>
                    <a href="<?php echo e(route('employment-type.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-user-tag"></i> Employment Type</a>
                    <?php endif; ?>
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read positions')): ?>
                    <a href="<?php echo e(route('position.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-briefcase"></i> Positions</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read violations')): ?>
                    <a href="<?php echo e(route('violation.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-triangle-exclamation"></i> Violations</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read leave-types')): ?>
                    <a href="<?php echo e(route('leave.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-calendar-check"></i>  Leaves</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read gsis-billing')): ?>
                    <a href="<?php echo e(route('gsis.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-bank"></i>  GSIS Billing</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read other-earnings')): ?>
                    <a href="<?php echo e(route('other-earnings.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-coins"></i>  Earnings</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read other-deductions')): ?>
                    <a href="<?php echo e(route('other-deductions.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-receipt"></i>  Deductions</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Timekeeping -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['read shift-schedule', 'read employee-schedule'])): ?>
                <div class="submenu-subgroup">
                    <p class="submenu-subtitle"><i class="fa-solid fa-id-card-clip"></i> Timekeeping</p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read shift-schedule')): ?>
                    <a href="<?php echo e(route('shift-schedule.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-briefcase"></i> Shift Schedule</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('read employee-schedule')): ?>
                    <a href="<?php echo e(route('employee-schedule.index')); ?>" class="submenu-item">
                        <i class="fa-solid fa-briefcase"></i> Employee Schedule</a>
                    <?php endif; ?>
                
                </div>
                <?php endif; ?>
            </div>
        </div>
     <?php endif; ?> 
    </div>

</div>
<?php /**PATH /var/www/html/oppapru_hris/resources/views/components/admin/sidebar-desk.blade.php ENDPATH**/ ?>