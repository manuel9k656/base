<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AdministracionController;
// $exitCode = Artisan::call('view:clear');
// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

// Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
//     Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
// });
Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {

    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

    // ========== RUTAS DE ADMINISTRACIÓN ==========
    Route::prefix('administracion')->name('administracion.')->group(function () {
        // === USUARIOS ===
        Route::middleware('can:ver-usuarios')->group(function () {
            Route::get('/usuarios', [AdministracionController::class, 'usuarios'])->name('usuarios');
            Route::post('/usuarios_datatable', [AdministracionController::class, 'usuarios_datatable'])->name('usuarios_datatable');
            Route::post('/usuarios/modal', [AdministracionController::class, 'nuevoUsuarioModal'])->name('usuarios.modal');
        });

        Route::middleware('can:crear-usuarios')->group(function () {
            Route::post('/usuarios/crear', [AdministracionController::class, 'crearUsuario'])->name('usuarios.crear');
        });

        Route::middleware('can:editar-usuarios')->group(function () {
            Route::post('/usuarios/{id}/password', [AdministracionController::class, 'cambiarPassword'])->name('usuarios.password');
        });

        Route::middleware('can:eliminar-usuarios')->group(function () {
            Route::delete('/usuarios/{id}', [AdministracionController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
        });

        Route::middleware('can:asignar-roles')->group(function () {
            Route::post('/usuarios/roles/modal', [AdministracionController::class, 'RolesUsuarioModal'])->name('usuarios.roles.modal');
            Route::post('/usuarios/{id}/roles', [AdministracionController::class, 'asignarRoles'])->name('usuarios.roles.asignar');
        });

        // === ROLES Y PERMISOS ===
        Route::middleware('can:ver-roles')->group(function () {
            Route::get('/roles', [AdministracionController::class, 'roles'])->name('roles');
            Route::post('/roles/modal', [AdministracionController::class, 'nuevoRolModal'])->name('roles.modal');
            Route::post('/roles/usuarios/modal', [AdministracionController::class, 'usuariosRolModal'])->name('roles.usuarios.modal');
        });

        Route::middleware('can:crear-roles')->group(function () {
            Route::post('/roles/crear', [AdministracionController::class, 'crearRol'])->name('roles.crear');
        });

        Route::middleware('can:eliminar-roles')->group(function () {
            Route::delete('/roles/{id}', [AdministracionController::class, 'eliminarRol'])->name('roles.eliminar');
        });
    });
});