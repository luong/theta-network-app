<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThetaController;

Route::get('/', [ThetaController::class, 'home'])->name('home');
Route::get('/account/{id}', [ThetaController::class, 'account'])->name('account');
Route::get('/transaction/{id}', [ThetaController::class, 'transaction'])->name('transaction');
