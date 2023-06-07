<?php

use App\Http\Livewire\ShowCatalog;
use App\Http\Livewire\ShowProduct;
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

Route::view('/', 'welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::view('/cart', 'cart')->name('cart');
    Route::get('/catalog', ShowCatalog::class)->name('catalog');
    Route::view('/checkout', 'checkout')->name('checkout');
    Route::view('/orders', 'orders')->name('orders');
    Route::view('/reports', 'reports')->name('reports');
});
