<?php

use App\Http\Livewire\ShowCart;
use App\Http\Livewire\ShowCatalog;
use App\Http\Livewire\ShowItem;
use App\Http\Livewire\ShowOrder;
use App\Http\Livewire\ShowOrders;
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
    'verified',
])->group(function () {
    Route::get('/cart', ShowCart::class)->name('cart.show');
    Route::get('/catalog', ShowCatalog::class)->name('catalog.show');
    Route::view('/image/{item}', 'image')->name('image');
    Route::get('/item/{item}/{location}', ShowItem::class)->name('item.show');
    Route::get('/orders', ShowOrders::class)->name('orders.show');
    Route::get('/order/{id}', ShowOrder::class)->name('order.show');
    Route::view('/reports', 'reports')->name('reports');
});
