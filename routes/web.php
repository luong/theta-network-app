<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThetaController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TempController;

Route::get('/', function () {
    return 'This website stopped. Any question, please reach us at https://twitter.com/ThetaPizza';
});

/*
Route::get('/', [ThetaController::class, 'home'])->name('home');
Route::get('/account/{id}', [ThetaController::class, 'account'])->name('account');
Route::get('/transaction/{id}', [ThetaController::class, 'transaction'])->name('transaction');
Route::get('/nft', [ThetaController::class, 'nft'])->name('nft');
Route::get('/whales/add/{id}', [ThetaController::class, 'addWhale'])->name('whales.add');
Route::get('/accounts', [ThetaController::class, 'accounts'])->name('accounts');
Route::get('/transactions', [ThetaController::class, 'transactions'])->name('transactions');
Route::get('/volumes', [ThetaController::class, 'volumes'])->name('volumes');
Route::get('/search', [ThetaController::class, 'search'])->name('search');

Route::get('/subscribe', [UsersController::class, 'subscribe'])->name('subscribe');
Route::post('/track-wallet', [UsersController::class, 'trackWallet'])->name('trackWallet');
Route::post('/untrack-wallet', [UsersController::class, 'untrackWallet'])->name('untrackWallet');

Route::get('/chart/theta-stake', [ThetaController::class, 'thetaStakeChart'])->name('thetaStakeChart');
Route::get('/chart/tfuel-stake', [ThetaController::class, 'tfuelStakeChart'])->name('tfuelStakeChart');
Route::get('/chart/tfuel-supply', [ThetaController::class, 'tfuelSupplyChart'])->name('tfuelStakeChart');
Route::get('/chart/tfuel-free-supply', [ThetaController::class, 'tfuelFreeSupplyChart'])->name('tfuelFreeSupplyChart');
Route::get('/chart/elite-node', [ThetaController::class, 'eliteNodeChart'])->name('eliteNodeChart');
Route::get('/chart/tfuel-burnt', [ThetaController::class, 'tfuelBurntChart'])->name('tfuelBurntChart');
Route::get('/chart/gold-ratio', [ThetaController::class, 'goldRatioChart'])->name('goldRatioChart');
Route::get('/chart/theta-drop-sales', [ThetaController::class, 'thetaDropSalesChart'])->name('thetaDropSalesChart');
Route::get('/chart/transactions', [ThetaController::class, 'transactionsChart'])->name('transactionsChart');
*/

Route::get('/camera/{id}', [TempController::class, 'camera']);
