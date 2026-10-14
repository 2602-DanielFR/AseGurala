<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\SesionClaseController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/docente/{dni}', [DocenteController::class, 'buscar']);
Route::view('/asistencia', 'asistencia');
Route::post('/sesion-clase/iniciar', [
    SesionClaseController::class,
    'iniciar'
])->name('sesion-clase.iniciar');

Route::post('/sesion-clase/finalizar', [
    SesionClaseController::class,
    'finalizar'
])->name('sesion-clase.finalizar');