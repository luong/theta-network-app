<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThetaController;

Route::get('/', [ThetaController::class, 'home'])->name('home');
Route::get('/account/{id}', [ThetaController::class, 'account'])->name('account');
Route::get('/transaction/{id}', [ThetaController::class, 'transaction'])->name('transaction');
Route::get('/nft', [ThetaController::class, 'nft'])->name('nft');
Route::get('/whales', [ThetaController::class, 'whales'])->name('whales');
Route::get('/whales/add/{id}', [ThetaController::class, 'addWhale'])->name('whales.add');

Route::get('/chart/theta-stake', [ThetaController::class, 'thetaStakeChart'])->name('thetaStakeChart');
Route::get('/chart/tfuel-stake', [ThetaController::class, 'tfuelStakeChart'])->name('tfuelStakeChart');
Route::get('/chart/tfuel-free-supply', [ThetaController::class, 'tfuelFreeSupplyChart'])->name('tfuelFreeSupplyChart');
Route::get('/chart/elite-node', [ThetaController::class, 'eliteNodeChart'])->name('eliteNodeChart');
