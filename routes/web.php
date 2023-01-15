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

////////////////////////////////
//管理者画面
////////////////////////////////

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

//bill
Route::get('/bill_page', [AdminController::class, 'bill_page']);
Route::get('/bill_search_event', [AdminController::class, 'bill_search_event']);
Route::get('/create_bill_invoice/{id}', [AdminController::class, 'create_bill_invoice']);
Route::get('/create_invoice_pdf/{id}', [AdminController::class, 'create_invoice_pdf']);

//negotiation
Route::get('/admin_negotiation_page', [AdminController::class, 'admin_negotiation_page']);
Route::get('/admin_suggest_price', [AdminController::class, 'admin_suggest_price']);


////////////////////////////////
//ユーザー画面
////////////////////////////////

Route::get('/', [HomeController::class, 'index']);
Route::get('/redirect', [HomeController::class, 'index']);
Route::get('/create_invoice_pdf_for_user/{id}', [HomeController::class, 'create_invoice_pdf_for_user']);
Route::get('/create_csv_for_user/{id}', [HomeController::class, 'create_csv_for_user']);


Route::get('/auction_page/{id}', [HomeController::class, 'auction_page']);
Route::get('watchList', [HomeController::class, 'watchList']);
Route::get('like/{id}', [HomeController::class, 'like']);
Route::get('unlike/{id}', [HomeController::class, 'unlike']);
Route::get('already_bid', [HomeController::class, 'already_bid']);
Route::get('bid_product_detail/{id}', [HomeController::class, 'bid_product_detail']);
Route::post('bid_product/{id}', [HomeController::class, 'bid_product']);
Route::post('change_product_order/{id}', [HomeController::class, 'change_product_order']);
Route::get('invoice_page', [HomeController::class, 'invoice_page']);
Route::get('user_negotiation_page', [HomeController::class, 'user_negotiation_page']);
Route::get('reject_suggest_price/{id}', [HomeController::class, 'reject_suggest_price']);
Route::get('accept_suggested_price', [HomeController::class, 'accept_suggested_price']);





Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
