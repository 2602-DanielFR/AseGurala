<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function mostrarLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'email' => 'El correo o la contraseña no son correctos.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $docentes = DB::table('docentes')->get();
        $cursos = DB::table('cursos')
            ->leftJoin('curso_alumno', 'cursos.id', '=', 'curso_alumno.curso_id')
            ->select(
                'cursos.id',
                'cursos.codigo',
                'cursos.nombre',
                DB::raw('COUNT(curso_alumno.alumno_id) as total_alumnos')
            )
            ->groupBy('cursos.id', 'cursos.codigo', 'cursos.nombre')
            ->get();

        $sesiones = DB::table('sesiones_clase')
            ->join('horarios', 'sesiones_clase.horario_id', '=', 'horarios.id')
            ->join('docente_curso', 'horarios.docente_curso_id', '=', 'docente_curso.id')
            ->join('cursos', 'docente_curso.curso_id', '=', 'cursos.id')
            ->join('docentes', 'docente_curso.docente_id', '=', 'docentes.id')
            ->select(
                'sesiones_clase.id',
                'sesiones_clase.fecha',
                'sesiones_clase.hora_inicio_real',
                'sesiones_clase.hora_fin_real',
                'sesiones_clase.estado',
                'cursos.codigo as curso_codigo',
                'cursos.nombre as curso_nombre',
                'horarios.seccion',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'docentes.dni as docente_dni'
            )
            ->orderBy('sesiones_clase.id', 'desc')
            ->limit(25)
            ->get();

        return view('dashboard', [
            'usuario' => Auth::user(),
            'docentes' => $docentes,
            'cursos' => $cursos,
            'sesiones' => $sesiones,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
