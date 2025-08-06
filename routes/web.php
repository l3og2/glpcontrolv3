<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DailyClosingController;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
| Aquí es donde puedes registrar las rutas web para tu aplicación.
*/

// --- Rutas Públicas (Invitados) ---
Route::get('/', function () {
    return view('welcome');
});

// --- Rutas que Requieren Autenticación ---
Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['role:Admin|Gerente Regional|Supervisor|Analista']) // Todos los roles pueden ver un dashboard
        ->name('dashboard');

    // PERFIL DE USUARIO
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // MOVIMIENTOS DE INVENTARIO
    Route::controller(InventoryMovementController::class)->prefix('movements')->name('movements.')->group(function () {
        // Rutas específicas primero
        Route::get('/batch-salida/create', 'createBatchSalida')->name('create_batch_salida');
        Route::post('/batch-salida', 'storeBatchSalida')->name('store_batch');
        Route::get('/batch/{batch_id}', 'showBatch')->name('show_batch');
        Route::patch('/{movement}/review', 'review')->name('review')->middleware('can:review movements');
        Route::patch('/{movement}/approve', 'approve')->name('approve')->middleware('can:approve movements');
        
        // Ruta resource al final
        Route::resource('/', InventoryMovementController::class)->except(['show', 'edit', 'update', 'destroy'])->parameters(['' => 'movement']);
    });

    // CIERRES DIARIOS
    Route::controller(DailyClosingController::class)->prefix('daily-closing')->name('daily-closing.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:perform daily closing');
        Route::get('/create', 'create')->name('create')->middleware('can:perform daily closing');
        Route::post('/', 'store')->name('store')->middleware('can:perform daily closing');
        Route::get('/{dailyClosing}', 'show')->name('show')->middleware('can:perform daily closing');
    });

    // REPORTES (Analistas, Gerentes y Admins)
    Route::controller(ReportController::class)->middleware('role:Admin|Gerente Regional|Analista')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/generate', 'generate')->name('generate');
        Route::post('/export', 'export')->name('export');
    });

});

// --- RUTAS DE ADMINISTRACIÓN (Solo para Admins) ---
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});


// --- RUTAS DE AUTENTICACIÓN ---
require __DIR__.'/auth.php';