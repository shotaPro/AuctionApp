<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//event
Route::get('/event_setting_page', [AdminController::class, 'event_setting_page']);
Route::get('/event_setting', [AdminController::class, 'event_setting']);
Route::get('/edit_event_page/{id}', [AdminController::class, 'edit_event_page']);
Route::post('/event_edit/{id}', [AdminController::class, 'event_edit']);
Route::get('/edit_delete/{id}', [AdminController::class, 'edit_delete']);


//register_product
Route::get('/register_product_page', [AdminController::class, 'register_product_page']);
Route::get('/register_product_page', [AdminController::class, 'register_product_page']);
Route::post('/register_product', [AdminController::class, 'register_product']);

//shuppin
Route::get('/shuppin_page', [AdminController::class, 'shuppin_page']);
Route::get('/search_shuppin_person', [AdminController::class, 'search_shuppin_person']);
Route::get('/select_shuppin_product', [AdminController::class, 'select_shuppin_product']);




Route::get('/', [HomeController::class, 'index']);
Route::get('/redirect', [HomeController::class, 'index']);


Route::get('/auction_page/{id}', [HomeController::class, 'auction_page']);
Route::get('watchList', [HomeController::class, 'watchList']);
Route::get('like/{id}', [HomeController::class, 'like']);
Route::get('unlike/{id}', [HomeController::class, 'unlike']);
Route::post('bid_product/{id}', [HomeController::class, 'bid_product']);




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
