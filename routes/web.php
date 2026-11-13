<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\SesionClaseController;

Route::get('/', function () {
    return redirect('/asistencia');
});

// Rutas de Usuarios Web (Panel Administrativo)
Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CRUDs Administrativos dentro del Panel
Route::get('/admin/docentes', [AdminController::class, 'indexDocentes']);
Route::post('/admin/docentes', [AdminController::class, 'guardarDocente']);
Route::delete('/admin/docentes/{id}', [AdminController::class, 'eliminarDocente']);

Route::get('/admin/cursos', [AdminController::class, 'indexCursos']);
Route::post('/admin/cursos', [AdminController::class, 'guardarCurso']);
Route::delete('/admin/cursos/{id}', [AdminController::class, 'eliminarCurso']);

Route::get('/admin/alumnos', [AdminController::class, 'indexAlumnos']);
Route::post('/admin/alumnos', [AdminController::class, 'guardarAlumno']);
Route::delete('/admin/alumnos/{id}', [AdminController::class, 'eliminarAlumno']);

Route::get('/admin/horarios', [AdminController::class, 'indexHorarios']);
Route::post('/admin/horarios', [AdminController::class, 'guardarAsignacion']);

// Rutas del Terminal de Control de Asistencia Docente + ZKTeco MB10-VL
Route::get('/docente/{dni}', [DocenteController::class, 'buscar']);
Route::view('/asistencia', 'asistencia');

Route::get('/asistencia/padron-alumnos', [
    SesionClaseController::class,
    'listarPadronCurso'
])->name('asistencia.padron');

Route::post('/sesion-clase/iniciar', [
    SesionClaseController::class,
    'iniciar'
])->name('sesion-clase.iniciar');

Route::post('/sesion-clase/finalizar', [
    SesionClaseController::class,
    'finalizar'
])->name('sesion-clase.finalizar');

Route::post('/sesion-clase/{sesion_id}/sincronizar-biometrico', [
    SesionClaseController::class,
    'sincronizarBiometrico'
])->name('sesion-clase.sesion.sincronizar');

Route::get('/sesion-clase/{sesion_id}/asistencias', [
    SesionClaseController::class,
    'listarAsistencia'
])->name('sesion-clase.asistencias');

Route::get('/asistencia/exportar-excel', [
    SesionClaseController::class,
    'exportarExcel'
])->name('asistencia.exportar-excel');

// Endpoint para recepción instantánea desde escuchar_tiempo_real.py
Route::post('/biometrico/marcar-instantaneo', [
    SesionClaseController::class,
    'marcarInstantaneo'
])->name('biometrico.instantaneo');
