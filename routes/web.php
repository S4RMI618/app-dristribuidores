<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerDetailController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('companies', CompanyController::class);

Route::resource('users', UserController::class);

Route::resource('customer-details', CustomerDetailController::class);

Route::get('/get-municipalities', [LocationController::class, 'getMunicipalities'])->name('get-municipalities');

require __DIR__.'/auth.php';
