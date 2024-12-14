<?php


use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HRISController;
use App\Http\Controllers\Admin\Job\ApplicantController;
use App\Http\Controllers\Admin\Job\InterviewController;
use App\Http\Controllers\Admin\Job\PostController;
use App\Http\Controllers\Admin\Job\RequirementsController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AnnouncementController as ESSAnnouncementController;
use App\Http\Controllers\Admin\ClockInOutController as ESSClockInOutController;
use App\Http\Controllers\Admin\LeaveController as ESSLeaveController;
use App\Http\Controllers\Admin\OfficialBusinessSlipController;
use App\Http\Controllers\Admin\Reports\DailyTimeRecord\DailyTimeRecordController;
use App\Http\Controllers\Admin\RequestStatusController as ESSRequestStatusController;
use App\Http\Controllers\Admin\Settings\HRIS\BankInformationController;
use App\Http\Controllers\Admin\Settings\HRIS\BatchConfigurationController;
use App\Http\Controllers\Admin\Settings\HRIS\BranchController;
use App\Http\Controllers\Admin\Settings\HRIS\CostCenterController;
use App\Http\Controllers\Admin\Settings\HRIS\DeductionController;
use App\Http\Controllers\Admin\Settings\HRIS\DepartmentController;
use App\Http\Controllers\Admin\Settings\HRIS\EmployeeStatusController;
use App\Http\Controllers\Admin\Settings\HRIS\GSISController;
use App\Http\Controllers\Admin\Settings\HRIS\PositionController;
use App\Http\Controllers\Admin\Settings\HRIS\ViolationController;
use App\Http\Controllers\Admin\Settings\HRIS\OtherDeductionsController;
use App\Http\Controllers\Admin\Settings\HRIS\OtherEarningsController;
use App\Http\Controllers\Admin\Settings\HRIS\SectionController;
use App\Http\Controllers\Admin\Settings\HRIS\LeaveController;
use App\Http\Controllers\Admin\Settings\ShiftScheduleController;
use App\Http\Controllers\Admin\Settings\CompanyInformationController;
use App\Http\Controllers\Admin\Settings\EmployeeScheduleController;
use App\Http\Controllers\Admin\TimeKeeping\TimekeepingController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Home\LoginController as HomeLoginController;
use App\Http\Controllers\Home\AppliedController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Home\ProfileController;
use App\Http\Controllers\Home\RegisterController;
use App\Http\Controllers\Home\ViewJobController;
use App\Http\Controllers\Home\InterviewController as HomeInterviewController;

use App\Http\Controllers\Employee\LoginController as EmployeeLoginController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\ClockInOutController as EmployeeClockInOutController;
use App\Http\Controllers\Employee\ATROController as EmployeeATROController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\AnnouncementController as EmployeeAnnouncementController;
use App\Http\Controllers\Employee\BusinessSlipController;
use App\Http\Controllers\Employee\DirectoryController as EmployeeDirectoryController;
use App\Http\Controllers\Employee\TeamController as EmployeeTeamController;
use App\Http\Controllers\Employee\RequestStatusController as EmployeeRequestStatusController;

use App\Http\Controllers\Home\SettingsController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])
        ->name('home.index')
        ->middleware('applicant:guest');
Route::get('view-job/{slug}', [ViewJobController::class, 'index'])
    ->name('home.view-job')
    ->middleware('applicant:guest');

Route::prefix('login')->group(function() {
    Route::get('/', [HomeLoginController::class, 'index'])
        ->name('home.login');
    Route::post('/', [HomeLoginController::class, 'store'])
        ->name('home.login');
});

Route::any('logout', [HomeLoginController::class, 'logout'])
    ->name('home.logout');

Route::prefix('register')->group(function() {
    Route::get('/', [RegisterController::class, 'index'])
        ->name('home.register');
    Route::post('/', [RegisterController::class, 'store'])
        ->name('home.register');
});
        

Route::middleware(['applicant'])->group(function() {
    Route::get('applied', [AppliedController::class, 'index'])
        ->name('home.applied');
    Route::get('applied/view-job/{slug}', [ViewJobController::class, 'index'])
        ->name('home.applied.view-job');
    Route::get('search/{search?}', [HomeController::class, 'index'])
        ->name('home.search');
    Route::resource('profile', ProfileController::class)
        ->names('home.profile');
    Route::get('assessment/respond/{job_id}/{interview_id}', [HomeInterviewController::class, 'interview'])
        ->name('interview-respond');
    Route::get('job/offer/upload/signed/{job_id}', [HomeInterviewController::class, 'offer'])
        ->name('upload-signed-offer');
    Route::get('job/requirements/upload/{job_id}', [HomeInterviewController::class, 'requirements'])
        ->name('upload-requirements');
});


    
Route::prefix('admin')->group(function() {
    Route::get('login', [AdminLoginController::class, 'index'])
        ->name('admin.index');
    Route::post('login', [AdminLoginController::class, 'login'])
        ->name('admin.login');
    Route::any('logout', [AdminLoginController::class, 'logout'])
        ->name('admin.logout');


    Route::middleware(['auth'])->group(function() {

        Route::get('dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::prefix('job')->group(function() {
    
            Route::resource('posts', PostController::class)->names('job.posts');
            Route::resource('assessments', InterviewController::class)->names('job.interview');
            Route::resource('requirements', RequirementsController::class)->names('job.requirements');
                    
            Route::get('applicants/{status}', [ApplicantController::class, 'index'])->name('job.applicants.index');
            Route::get('applicants/{status}/create', [ApplicantController::class, 'create'])->name('job.applicants.create');
            Route::post('applicants/{status}', [ApplicantController::class, 'store'])->name('job.applicants.store');
            Route::get('applicants/{status}/{applicant}', [ApplicantController::class, 'show'])->name('job.applicants.show');
            Route::get('applicants/{status}/{applicant}/edit', [ApplicantController::class, 'edit'])->name('job.applicants.edit');
            Route::put('applicants/{status}/{applicant}', [ApplicantController::class, 'update'])->name('job.applicants.update');
            Route::delete('applicants/{status}/{applicant}', [ApplicantController::class, 'destroy'])->name('job.applicants.destroy');
        
        });
        
        Route::get('hris', [HRISController::class, 'index'])
            ->name('hris.index');
        Route::get('hris/employee/{employee_no?}', [HRISController::class, 'show'])
            ->name('hris.show');

        Route::get('hris/manual', [HRISController::class, 'manual'])
            ->name('hris.manual');
        
        Route::prefix('timekeeping')->group(function() {
            Route::get('logs/{month?}/{day?}/{year?}', [TimekeepingController::class, 'index'])
                ->name('timekeeping.index');
            Route::get('upload', [TimekeepingController::class, 'upload'])
                ->name('timekeeping.upload');
        });

        Route::prefix('ess')->group(function() {
            Route::get('official-business-slip', [OfficialBusinessSlipController::class, 'index'])
                ->name('ess.obs.index');

            Route::get('leave', [ESSLeaveController::class, 'index'])
                ->name('ess.leave');
        
            Route::prefix('announcements')->group(function() {
                Route::get('/', [ESSAnnouncementController::class, 'index'])
                    ->name('ess.announcements.index');
                Route::get('apply', [ESSAnnouncementController::class, 'create'])
                    ->name('ess.announcements.create');
                Route::get('edit/{id}', [ESSAnnouncementController::class, 'edit'])
                    ->name('ess.announcements.edit');
            });

            Route::prefix('request-status')->group(function() {
                Route::get('{id?}', [ESSRequestStatusController::class, 'index'])
                    ->name('ess.request-status');
            });
        });

        Route::prefix('reports')->group( function() {
            Route::get('daily-time-record', function () {
                $title = 'Daily Time Record';
                return view('admin.reports.daily-time-record.index', compact('title'));
            })->name('reports.dtr');

            Route::get('/reports/{date?}', [DailyTimeRecordController::class, 'index'])->name('dtr.index');

        });
        
        Route::prefix('settings')->group( function() {
        
            Route::get('company-information', [CompanyInformationController::class, 'index'])
                ->name('company.index');

            Route::prefix('hris')->group( function() {
        
                Route::prefix('location-management')->group( function() {
                    Route::resource('/branch', BranchController::class)
                        ->names('branch');
        
                    Route::resource('/cost-center', CostCenterController::class)
                        ->names('cost-center');
        
                    Route::resource('/department', DepartmentController::class)
                        ->names('department');

                    Route::resource('/section', SectionController::class)
                        ->names('section');
                });
        
                Route::resource('bank-information', BankInformationController::class)
                    ->names('bank-information');    
        
                Route::resource('batch-configuration', BatchConfigurationController::class)
                    ->names('batch-configuration');           
        
                Route::resource('employee-status', EmployeeStatusController::class)
                    ->names('employee-status');
        
                Route::resource('position', PositionController::class)
                    ->names('position');
        
                Route::resource('violation', ViolationController::class)
                    ->names('violation');
                
                Route::resource('leave', LeaveController::class)
                    ->names('leave');

                Route::resource('gsis', GSISController::class)
                    ->names('gsis');

                Route::resource('other-earnings', OtherEarningsController::class)
                    ->names('other-earnings');

                Route::resource('other-deductions', OtherDeductionsController::class)
                    ->names('other-deductions');

                Route::get('employee/deductions/{id}', [DeductionController::class, 'index'])
                    ->name('deductions.index');

                Route::post('employee/deductions/{id}', [DeductionController::class, 'create'])
                    ->name('deductions.create');

            });

            Route::resource('shift-schedule', ShiftScheduleController::class)
                ->names('shift-schedule');

            Route::resource('employee-schedule', EmployeeScheduleController::class)
                ->names('employee-schedule');
        
            Route::prefix('users')->group(function() {
                Route::get('{type}', [UserController::class, 'index'])
                    ->name('users.index');
            });
            
        });
    });

});

Route::prefix('employee')->group(function() {
    Route::prefix('login')->group(function() {
        Route::get('/', [EmployeeLoginController::class, 'index'])
            ->name('employee.login');
        Route::post('/', [EmployeeLoginController::class, 'store'])
            ->name('employee.login');
        Route::any('logout', [EmployeeLoginController::class, 'logout'])
            ->name('employee.logout');
    });

    Route::middleware('employee')->group(function() {
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('employee.dashboard');
        
        Route::prefix('leave')->group(function() {

            Route::get('/', [EmployeeLeaveController::class, 'index'])
                ->name('employee.leave');
            Route::get('apply', [EmployeeLeaveController::class, 'create'])
                ->name('employee.leave.apply');
            Route::get('edit/{id}', [EmployeeLeaveController::class, 'edit'])
                ->name('employee.leave.edit');
        });

        Route::prefix('official-business-slip')->group(function() {

            Route::get('/', [BusinessSlipController::class, 'index'])
                ->name('employee.obs.index');
            Route::get('apply', [BusinessSlipController::class, 'create'])
                ->name('employee.obs.apply');
            Route::get('edit/{id}', [BusinessSlipController::class, 'edit'])
                ->name('employee.obs.edit');
        });

        Route::prefix('authority-to-render-time')->group(function() {

            Route::get('/', [EmployeeATROController::class, 'index'])
                ->name('employee.atro');
            Route::get('apply', [EmployeeATROController::class, 'create'])
                ->name('employee.atro.apply');
            Route::get('edit/{id}', [EmployeeATROController::class, 'edit'])
                ->name('employee.atro.edit');
                
        });

        Route::get('clock-in-out', [EmployeeClockInOutController::class, 'index'])
            ->name('employee.clock');

        Route::get('directory', [EmployeeDirectoryController::class, 'index'])
            ->name('employee.directory');
        
        Route::get('team', [EmployeeTeamController::class, 'index'])
            ->name('employee.team');

        Route::get('request-status', [EmployeeRequestStatusController::class, 'index'])
            ->name('employee.request-status');

        Route::get('announcements', [EmployeeAnnouncementController::class, 'index'])
            ->name('employee.announcements.index');
        Route::get('announcements/{id}', [EmployeeAnnouncementController::class, 'view'])
            ->name('employee.announcements.view');

        Route::get('profile', [EmployeeProfileController::class, 'index'])
            ->name('employee.profile');

    });
});