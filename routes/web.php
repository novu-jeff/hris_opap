<?php

use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HRISController;
use App\Http\Controllers\Admin\Job\ApplicantController;
use App\Http\Controllers\Admin\Job\InterviewController;
use App\Http\Controllers\Admin\Job\PostController;
use App\Http\Controllers\Admin\Job\RequirementsController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\RequestStatusController;
use App\Http\Controllers\Admin\Settings\HRIS\BankInformationController;
use App\Http\Controllers\Admin\Settings\HRIS\BatchConfigurationController;
use App\Http\Controllers\Admin\Settings\HRIS\BranchController;
use App\Http\Controllers\Admin\Settings\HRIS\CostCenterController;
use App\Http\Controllers\Admin\Settings\HRIS\DepartmentCenterController;
use App\Http\Controllers\Admin\Settings\HRIS\EmployeeStatusController;
use App\Http\Controllers\Admin\Settings\HRIS\PositionController;
use App\Http\Controllers\Admin\Settings\HRIS\ViolationController;
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
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\AnnouncementController as EmployeeAnnouncementController;
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
    Route::get('view-job/{slug}', [ViewJobController::class, 'index'])
        ->name('home.view-job')
        ->middleware(Auth::guard('applicant')->check() ? ['applicant'] : []);
    Route::get('applied/view-job/{slug}', [ViewJobController::class, 'index'])
        ->name('home.applied.view-job');
    Route::get('search/{search?}', [HomeController::class, 'index'])
        ->name('home.search');
    Route::resource('profile', ProfileController::class)
        ->names('home.profile');
    Route::get('interview/respond/{job_id}/{interview_id}', [HomeInterviewController::class, 'interview'])
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
});

Route::prefix('job')->group(function() {
    
    # Job Posts Routes
    Route::resource('posts', PostController::class)->names('job.posts');
    Route::resource('interviews', InterviewController::class)->names('job.interview');
    Route::resource('requirements', RequirementsController::class)->names('job.requirements');
    
    // Route::resource('applicants/{status}', ApplicantController::class)->names('job.applicants');

    Route::get('applicants/{status}', [ApplicantController::class, 'index'])->name('job.applicants.index');
    Route::get('applicants/{status}/create', [ApplicantController::class, 'create'])->name('job.applicants.create');
    Route::post('applicants/{status}', [ApplicantController::class, 'store'])->name('job.applicants.store');
    Route::get('applicants/{status}/{applicant}', [ApplicantController::class, 'show'])->name('job.applicants.show');
    Route::get('applicants/{status}/{applicant}/edit', [ApplicantController::class, 'edit'])->name('job.applicants.edit');
    Route::put('applicants/{status}/{applicant}', [ApplicantController::class, 'update'])->name('job.applicants.update');
    Route::delete('applicants/{status}/{applicant}', [ApplicantController::class, 'destroy'])->name('job.applicants.destroy');

});

Route::resource('hris', HRISController::class)
    ->names('hris');

Route::prefix('ess')->group(function() {
    Route::get('leave', [LeaveController::class, 'index'])
        ->name('ess.leave');
    Route::prefix('announcements')->group(function() {
        Route::get('/', [AnnouncementController::class, 'index'])
            ->name('ess.announcements.index');
        Route::get('apply', [AnnouncementController::class, 'create'])
            ->name('ess.announcements.create');
        Route::get('edit/{id}', [AnnouncementController::class, 'edit'])
            ->name('ess.announcements.edit');
    });

    Route::prefix('request-status')->group(function() {
        Route::get('{id?}', [RequestStatusController::class, 'index'])
            ->name('ess.request-status');
    });
});

Route::prefix('settings')->group( function() {

    Route::prefix('hris')->group( function() {

        Route::prefix('location-management')->group( function() {
            Route::resource('/branch', BranchController::class)
                ->names('branch');

            Route::resource('/cost-center', CostCenterController::class)
                ->names('cost-center');

            Route::resource('/department-center', DepartmentCenterController::class)
                ->names('department-center');
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

    });

    Route::prefix('users')->group(function() {
        Route::get('{type}', [UserController::class, 'index'])
            ->name('users.index');
    });
    
});

Route::prefix('employee')->group(function() {
    Route::prefix('login')->group(function() {
        Route::get('/', [EmployeeLoginController::class, 'index'])
            ->name('employee.login');
        Route::post('/', [EmployeeLoginController::class, 'store'])
            ->name('login');
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