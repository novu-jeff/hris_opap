<?php

use App\Http\Controllers\Admin\Job\ApplicantController;
use App\Http\Controllers\Admin\Job\InterviewController;
use App\Http\Controllers\Admin\Job\PostController;
use App\Http\Controllers\Admin\Job\RequirementsController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;

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
    Route::resource('applicants/{status}', ApplicantController::class)->names('job.applicants');
    
    // Route::get('applicants/{status}', [ApplicantStatusController::class, 'index']);
});
