<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\NetworkController;

Route::any('/login', [AuthController::class, 'login'])->name('admin.login');

Route::middleware('auth:admin')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('admin.home');

    Route::get('/validators', [NetworkController::class, 'validators'])->name('admin.validators');
    Route::any('/validator/add', [NetworkController::class, 'addValidator'])->name('admin.validator.add');
    Route::any('/validator/edit/{id}', [NetworkController::class, 'editValidator'])->name('admin.validator.edit');
    Route::get('/validator/delete/{id}', [NetworkController::class, 'deleteValidator'])->name('admin.validator.delete');

    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});



