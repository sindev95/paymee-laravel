<?php

use Illuminate\Support\Facades\Route;
use Sindev95\Paymee\Controllers\PaymeeController;

Route::get('/paymee/{order_id}/{total}',[PaymeeController::class,'generate_paymee_form'])->name('paymee.pay');
Route::get('/paymee/success',[PaymeeController::class,'paymee_success'])->name('paymee.success');
Route::get('/paymee/failed',[PaymeeController::class,'paymee_failed'])->name('paymee.failed');
