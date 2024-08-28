<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\MailChimpController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProController;
use App\View\Components\Livewire;
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


// Route::group(['prefix' => "/"], function () {
//     Route::get('/', [DataController::class, 'index'])->name('home');
//     Route::post('/addData', [DataController::class, 'addData'])->name('adddata');
//     Route::get('/getdata/{id}', [DataController::class, 'getData'])->name('getdata');
//     Route::post('/delete', [DataController::class, 'remove'])->name('delete');
// });

// Route::get('/', [Livewire::class]);


// Route::get('/getattribute', [ProController::class, 'getAttribute'])->name('get.attribute');

// Route::get('/', [OcrController::class, 'index'])->name('home');
// Route::post('/image-extraction', [OcrController::class, 'imageExtraction'])->name('image.reader');

// Route::get("/captcha", [CaptchaController::class, 'index']);
// Route::post('/capt', [CaptchaController::class, 'post'])->name("post");

Route::get('/mailchimp', [MailChimpController::class, 'index']);
Route::post('/mailchimp', [MailChimpController::class, 'post'])->name('mailchimp');

Route::get('/callback', [MailChimpController::class, 'callback'])->name('payment.callback');


// Route::get('/product', [ProController::class, 'index']);
// Route::get('/{id}', [ProController::class, 'show'])->name('product.show');
// Route::post('/pro', [ProController::class, 'addPro'])->name('products.store');
// Route::post('/product/{product}/sku', [ProController::class, 'getSku']);

Route::get('/payment', [PaymentController::class, 'index']);
Route::post('/payment', [PaymentController::class, 'createUPIPaymentRequest'])->name('request.upi.payment');
Route::post('/confirm',[PaymentController::class,'confirmPayment'])->name('razorpay.payment.store');
