<?php

use Illuminate\Support\Facades\Route;

// Customer controllers
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\ProfileController;

// Admin controllers
use App\Http\Controllers\Admin\OrderController as AdminOrderController;

// Vendor controllers
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes (Blade views)
Route::get('/', [CustomerProductController::class, 'index'])->name('home');
Route::get('/products/{product}', [CustomerProductController::class, 'show'])->name('products.show');

// Authenticated routes for frontend (Blade views)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile pages
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================
// 🔹 ADMIN ROUTES (Blade views)
// ==========================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
});

// ==========================
// 🔹 VENDOR ROUTES (Blade views)
// ==========================
Route::prefix('vendor')->middleware(['auth', 'role:vendor'])->name('vendor.')->group(function () {
    Route::get('/dashboard', fn() => view('vendor.dashboard'))->name('dashboard');
});

// Keep auth routes for browser (login/register forms, etc.)
require __DIR__ . '/auth.php';
