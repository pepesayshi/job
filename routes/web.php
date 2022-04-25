<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use App\Models\Jobs;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

/** Router for home or all jobs */
Route::get('/{home?}', [App\Http\Controllers\JobController::class, 'index'])->name('home')->where('home', 'home');
Route::get('/jobs', [App\Http\Controllers\JobController::class, 'index'])->name('jobs')->where('home', 'home');

/** Router for creating a new job */
Route::get('/job/new', [App\Http\Controllers\JobController::class, 'new'])->name('jobnew');

/** Router for saving a job detalils */
Route::post('/job/update', [App\Http\Controllers\JobController::class, 'update'])->name('jobupdate');

/** Router for apply filters to the list of jobs */
Route::post('/job/search', [App\Http\Controllers\JobController::class, 'search'])->name('jobsearch');

/** Router for posting a new job form */
Route::post('/job/new/submit', [App\Http\Controllers\JobController::class, 'create'])->name('jobcreate');

/** Router for a single job */
Route::get('/job/{id}', [App\Http\Controllers\JobController::class, 'view'])->name('jobsingle')->where('id', '^[0-9]*$');