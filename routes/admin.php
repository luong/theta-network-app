<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\NetworkController;
use App\Http\Controllers\Admin\AdminController;

Route::any('/login', [AuthController::class, 'login'])->name('admin.login');

Route::middleware('auth:admin')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('admin.home');
    Route::post('/run', [HomeController::class, 'run'])->name('admin.run');

    Route::get('/admins', [AdminController::class, 'list'])->name('admin.admins');
    Route::any('/admin/add', [AdminController::class, 'add'])->name('admin.admin.add');
    Route::any('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.admin.edit');
    Route::get('/admin/delete/{id}', [AdminController::class, 'delete'])->name('admin.admin.delete');

    Route::get('/validators', [NetworkController::class, 'validators'])->name('admin.validators');
    Route::any('/validator/add', [NetworkController::class, 'addValidator'])->name('admin.validator.add');
    Route::any('/validator/edit/{id}', [NetworkController::class, 'editValidator'])->name('admin.validator.edit');
    Route::get('/validator/delete/{id}', [NetworkController::class, 'deleteValidator'])->name('admin.validator.delete');

    Route::get('/holders', [NetworkController::class, 'holders'])->name('admin.holders');
    Route::any('/holder/add', [NetworkController::class, 'addHolder'])->name('admin.holder.add');
    Route::any('/holder/edit/{id}', [NetworkController::class, 'editHolder'])->name('admin.holder.edit');
    Route::get('/holder/delete/{id}', [NetworkController::class, 'deleteHolder'])->name('admin.holder.delete');

    Route::get('/top-activists', [NetworkController::class, 'topActivists'])->name('admin.topActivists');

    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});



