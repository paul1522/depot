<?php

use App\Http\Livewire\CartShow;
use App\Http\Livewire\ShowCatalog;
use App\Http\Livewire\ShowItem;
use Illuminate\Support\Facades\Route;
use App\Models\Item;

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
    Route::get('/cart', CartShow::class)->name('cart.show');
    Route::get('/catalog', ShowCatalog::class)->name('catalog.show');
    Route::view('/checkout', 'checkout')->name('checkout');
    Route::get('/item/{id}', ShowItem::class)->name('item.show');
    Route::view('/orders', 'orders')->name('orders');
    Route::view('/reports', 'reports')->name('reports');
});
