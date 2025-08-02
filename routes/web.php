<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DailyClosingController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('movements/batch-salida/create', [InventoryMovementController::class, 'createBatchSalida'])->name('movements.create_batch_salida');
    Route::get('movements/batch/{batch_id}', [InventoryMovementController::class, 'showBatch'])->name('movements.show_batch');
    Route::post('movements/batch-salida', [InventoryMovementController::class, 'storeBatchSalida'])->name('movements.store_batch');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/movements/{movement}/review', [InventoryMovementController::class, 'review'])->name('movements.review')->middleware('can:review movements');
    Route::patch('/movements/{movement}/approve', [InventoryMovementController::class, 'approve'])->name('movements.approve')->middleware('can:approve movements');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('movements', InventoryMovementController::class)->middleware('can:create movements');
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', 'role:Admin|Gerente Regional|Supervisor|Analista'])->name('dashboard');
    Route::get('/daily-closing/create', [DailyClosingController::class, 'create'])->name('daily-closing.create');
    Route::post('/daily-closing', [DailyClosingController::class, 'store'])->name('daily-closing.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    // 'auth' => El usuario debe estar logueado.
    // 'role:Admin' => El usuario logueado DEBE tener el rol 'Admin'.
    // 'prefix('admin')' => Todas las URLs empezarán con /admin (ej: /admin/users)
    // 'name('admin.')' => Todas las rutas tendrán el prefijo 'admin.' (ej: admin.users.create)
            
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
