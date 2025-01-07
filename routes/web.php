<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'dashboard']);
Route::get('/categories', [CategoryController::class, 'category']);
Route::get('/products', [ProductController::class, 'product'])->name('products');
Route::get('/users', [UserController::class, 'user']);
Route::get('/order', [OrderController::class, 'order'])->name('order');
Route::get('/history', [HistoryController::class, 'history']);
Route::get('/order/checkout', [CheckoutController::class, 'checkout'])->name('order.checkout');
Route::get('receipt/{order}/print', [ReceiptController::class, 'print'])->name('receipt.print');
