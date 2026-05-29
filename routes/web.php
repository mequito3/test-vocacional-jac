<?php

use App\Http\Controllers\ResultadoController;
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
