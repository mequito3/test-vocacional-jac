<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResultadoController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas web - Test Vocacional CHASIDE / JAC Bolivia 2000
|--------------------------------------------------------------------------
*/

Route::get('/',               [TestController::class, 'welcome'])->name('welcome');
Route::get('/registro',       [TestController::class, 'registro'])->name('registro');
Route::post('/registro',      [TestController::class, 'guardarRegistro'])->name('registro.guardar');
Route::get('/test',           [TestController::class, 'test'])->name('test');
Route::post('/resultado',     [ResultadoController::class, 'calcular'])->name('resultado.calcular');
Route::get('/resultado/{id}', [ResultadoController::class, 'mostrar'])->name('resultado.mostrar');
Route::get('/resultado/{id}/pdf', [ResultadoController::class, 'pdf'])->name('resultado.pdf');
Route::get('/reiniciar',      [TestController::class, 'reiniciar'])->name('reiniciar');

// Enlace de grupo para colegios (guarda el colegio en sesion)
Route::get('/grupo/{token}',  [TestController::class, 'grupo'])->name('grupo');

// Resultado publico compartido (sin necesidad de sesion)
Route::get('/r/{token}',      [ShareController::class, 'ver'])->name('resultado.share');
Route::get('/r/{token}/pdf',  [ShareController::class, 'pdf'])->name('resultado.share.pdf');

// Panel de administracion
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout',[AdminController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/',                       [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/colegios',              [AdminController::class, 'crearColegio'])->name('colegios.crear');
        Route::delete('/colegios/{id}',       [AdminController::class, 'eliminarColegio'])->name('colegios.eliminar');
        Route::get('/colegios/{id}',          [AdminController::class, 'verColegio'])->name('colegios.ver');
        Route::get('/sin-colegio',            [AdminController::class, 'sinColegio'])->name('sin_colegio');
    });
});
