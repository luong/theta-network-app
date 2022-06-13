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

    Route::get('/accounts', [NetworkController::class, 'accounts'])->name('admin.accounts');
    Route::any('/account/add', [NetworkController::class, 'addAccount'])->name('admin.account.add');
    Route::any('/account/edit/{id}', [NetworkController::class, 'editAccount'])->name('admin.account.edit');
    Route::get('/account/delete/{id}', [NetworkController::class, 'deleteAccount'])->name('admin.account.delete');

    Route::get('/top-activists', [NetworkController::class, 'topActivists'])->name('admin.topActivists');
    Route::get('/validators', [NetworkController::class, 'validators'])->name('admin.validators');
    Route::get('/transactions', [NetworkController::class, 'transactions'])->name('admin.transactions');
    Route::get('/stakes', [NetworkController::class, 'stakes'])->name('admin.stakes');

    Route::get('/logs', [NetworkController::class, 'logs'])->name('admin.logs');

    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});



