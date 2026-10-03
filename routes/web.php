<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\ProductController;

Route::get('/', fn () => view('landing'))->name('landing');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');

    Route::resource('orders', OrderController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('customers', CustomerController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
Route::get('/reports', [OrderController::class, 'report'])->name('reports');
Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
});

require __DIR__.'/auth.php';
