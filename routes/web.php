<?php

use App\Http\Controllers\Admin\HRISController;
use App\Http\Controllers\Admin\Job\ApplicantController;
use App\Http\Controllers\Admin\Job\InterviewController;
use App\Http\Controllers\Admin\Job\PostController;
use App\Http\Controllers\Admin\Job\RequirementsController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Settings\HRIS\BankInformationController;
use App\Http\Controllers\Admin\Settings\HRIS\BatchConfigurationController;
use App\Http\Controllers\Admin\Settings\HRIS\BranchController;
use App\Http\Controllers\Admin\Settings\HRIS\CostCenterController;
use App\Http\Controllers\Admin\Settings\HRIS\DepartmentCenterController;
use App\Http\Controllers\Admin\Settings\HRIS\EmployeeStatusController;
use App\Http\Controllers\Admin\Settings\HRIS\PositionController;
use App\Http\Controllers\Admin\Settings\HRIS\ViolationController;
use App\Http\Controllers\Home\LoginController as HomeLoginController;
use App\Http\Controllers\Home\AppliedController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Home\ProfileController;
use App\Http\Controllers\Home\RegisterController;
use App\Http\Controllers\Home\ViewJobController;
use App\Livewire\Admin\Job\Create;
use App\Livewire\Admin\Job\Index;
use App\Livewire\Admin\Job\Update;
use Illuminate\Support\Facades\Route;

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
        ->name('home.index');

Route::prefix('login')->group(function() {
    Route::get('/', [HomeLoginController::class, 'index'])
        ->name('login');
    Route::post('/', [HomeLoginController::class, 'store'])
        ->name('login');
});

Route::any('logout', [HomeLoginController::class, 'logout'])
    ->name('logout');

Route::prefix('register')->group(function() {
    Route::get('/', [RegisterController::class, 'index'])
        ->name('register');
    Route::post('/', [RegisterController::class, 'store'])
        ->name('register');
});
        

Route::middleware(['applicant'])->group(function() {
    Route::get('applied', [AppliedController::class, 'index'])
    ->name('home.applied');
    Route::get('view-job/{slug}', [ViewJobController::class, 'index'])
        ->name('home.view-job');
    Route::get('applied/view-job/{slug}', [ViewJobController::class, 'index'])
        ->name('home.applied.view-job');
    Route::get('search/{search?}', [HomeController::class, 'index'])
        ->name('home.search');
    Route::resource('profile', ProfileController::class)
        ->names('home.profile');
});
    
Route::prefix('admin')->group(function() {
    Route::get('login', [AdminLoginController::class, 'index'])
        ->name('admin.index');
    Route::post('login', [AdminLoginController::class, 'login'])
        ->name('admin.login');
    Route::any('logout', [AdminLoginController::class, 'logout'])
        ->name('logout');
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
    });
