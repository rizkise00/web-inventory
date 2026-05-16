<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stock-in/export', [StockInController::class, 'export'])->name('stock-in.export');
    Route::resource('stock-in', StockInController::class);
    Route::get('/stock-out/export', [StockOutController::class, 'export'])->name('stock-out.export');
    Route::resource('stock-out', StockOutController::class);
    Route::get('/maintenances/export', [MaintenanceController::class, 'export'])->name('maintenances.export');
    Route::resource('maintenances', MaintenanceController::class);
});

// Manager only - User management and Product/Category management
Route::middleware(['auth', 'approved', 'manager'])->group(function () {
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::get('/items/export', [ItemController::class, 'export'])->name('items.export');
    Route::resource('items', ItemController::class);
    Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::resource('categories', CategoryController::class);
});
