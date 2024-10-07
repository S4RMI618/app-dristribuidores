<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerDetailController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas protegidas de compañías, usuarios, detalles de clientes y órdenes

    Route::resource('companies', CompanyController::class)->middleware([RoleMiddleware::class]);
    Route::resource('users', UserController::class)->middleware([RoleMiddleware::class]);
    Route::resource('customer-details', CustomerDetailController::class);
    Route::resource('orders', OrderController::class);

});

/* Rutas secundarias */
Route::get('/get-municipalities', [LocationController::class, 'getMunicipalities'])->name('get-municipalities');
Route::get('/products/search', [ProductController::class, 'searchProducts'])->name('products.search');


require __DIR__ . '/auth.php';
