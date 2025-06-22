<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    // 'auth' => El usuario debe estar logueado.
    // 'role:Admin' => El usuario logueado DEBE tener el rol 'Admin'.
    // 'prefix('admin')' => Todas las URLs empezarán con /admin (ej: /admin/users)
    // 'name('admin.')' => Todas las rutas tendrán el prefijo 'admin.' (ej: admin.users.create)
            
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
