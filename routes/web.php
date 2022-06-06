<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThetaController;

Route::get('/', [ThetaController::class, 'home'])->name('home');
