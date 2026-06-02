<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\FrontController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Admin\AdminController;

// --- RUTE PELANGGAN ---
Route::get('/', [FrontController::class, 'landing'])->name('landing');
Route::post('/select-type', [FrontController::class, 'selectType'])->name('order.select-type');
Route::get('/menu', [FrontController::class, 'menu'])->name('front.menu');

Route::post('/cart/save', [CartController::class, 'saveCart'])->name('cart.save');
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/checkout/process', [CartController::class, 'processOrder'])->name('cart.process');
Route::get('/order/success', [CartController::class, 'successPage'])->name('order.success');

// --- RUTE KASIR (ADMIN) ---
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/order/{id}/update', [AdminController::class, 'updateStatus'])->name('admin.order.update');
Route::get('/admin/report', [AdminController::class, 'report'])->name('admin.report');