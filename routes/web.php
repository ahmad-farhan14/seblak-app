<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\FrontController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Customer\CartController;

// Jalur Pelanggan
Route::get('/', [FrontController::class, 'landing'])->name('landing');
Route::post('/select-type', [FrontController::class, 'selectType'])->name('order.select-type');
Route::get('/menu', [FrontController::class, 'menu'])->name('front.menu');

// Jalur Admin Sementara (Langsung oper ke tampilan)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Admin: Tambah Menu
Route::get('/admin/menus/create', [MenuController::class, 'create'])->name('admin.menus.create');
Route::post('/admin/menus', [MenuController::class, 'store'])->name('admin.menus.store');
Route::post('/cart/save', [MenuController::class, 'saveCart'])->name('cart.save');
Route::post('/cart/save', [CartController::class, 'saveCart'])->name('cart.save');
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/checkout/process', [CartController::class, 'processOrder'])->name('cart.process');