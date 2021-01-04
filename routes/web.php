<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\StripeController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::any('/', [MainController::class, 'index']);

/*** Stripe Routes ***/
Route::prefix('stripe')->group(function () {
    Route::get('/', [StripeController::class, 'index']);
    Route::post('/pre-auth', [StripeController::class, 'preAuth']);
    Route::post('/store', [StripeController::class, 'store']);
});

// Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
