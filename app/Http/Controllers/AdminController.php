<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    private function verificarAdmin()
    {
        if (!Auth::check()) {
            abort(redirect('/login'));
        }
    }

    // --- CRUD DOCENTES ---
    public function indexDocentes()
    {
        $this->verificarAdmin();
        $docentes = DB::table('docentes')->orderBy('apellidos')->get();
        return view('admin.docentes', compact('docentes'));
    }

    public function guardarDocente(Request $request)
    {
        $this->verificarAdmin();
        $datos = $request->validate([
            'dni' => 'required|string|max:15|unique:docentes,dni',
            'nombres' => 'required|string|max:120',
            'apellidos' => 'required|string|max:120',
            'email' => 'nullable|email|max:120',
        ]);

        DB::table('docentes')->insert([
            'dni' => $datos['dni'],
            'nombres' => strtoupper($datos['nombres']),
            'apellidos' => strtoupper($datos['apellidos']),
            'email' => $datos['email'],
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/docentes')->with('exito', 'Docente registrado correctamente.');
    }

    public function eliminarDocente($id)
    {
        $this->verificarAdmin();
        DB::table('docentes')->where('id', $id)->delete();
        return redirect('/admin/docentes')->with('exito', 'Docente eliminado correctamente.');
    }

    // --- CRUD CURSOS ---
    public function indexCursos()
    {
        $this->verificarAdmin();
        $cursos = DB::table('cursos')->orderBy('nombre')->get();
        return view('admin.cursos', compact('cursos'));
    }

    public function guardarCurso(Request $request)
    {
        $this->verificarAdmin();
        $datos = $request->validate([
            'codigo' => 'required|string|max:30|unique:cursos,codigo',
            'nombre' => 'required|string|max:150',
        ]);

        DB::table('cursos')->insert([
            'codigo' => strtoupper($datos['codigo']),
            'nombre' => strtoupper($datos['nombre']),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/cursos')->with('exito', 'Curso creado correctamente.');
    }

    public function eliminarCurso($id)
    {
        $this->verificarAdmin();
        DB::table('cursos')->where('id', $id)->delete();
        return redirect('/admin/cursos')->with('exito', 'Curso eliminado correctamente.');
    }

    // --- CRUD ALUMNOS ---
    public function indexAlumnos()
    {
        $this->verificarAdmin();
        $alumnos = DB::table('alumnos')->orderBy('apellidos')->get();
        return view('admin.alumnos', compact('alumnos'));
    }

    public function guardarAlumno(Request $request)
    {
        $this->verificarAdmin();
        $datos = $request->validate([
            'dni' => 'required|string|max:20|unique:alumnos,dni',
            'codigo' => 'nullable|string|max:30',
            'nombres' => 'required|string|max:120',
            'apellidos' => 'required|string|max:120',
            'email' => 'nullable|email|max:120',
        ]);

        DB::table('alumnos')->insert([
            'dni' => $datos['dni'],
            'codigo' => $datos['codigo'] ?? $datos['dni'],
            'nombres' => strtoupper($datos['nombres']),
            'apellidos' => strtoupper($datos['apellidos']),
            'email' => $datos['email'],
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/alumnos')->with('exito', 'Alumno registrado correctamente.');
    }

    public function eliminarAlumno($id)
    {
        $this->verificarAdmin();
        DB::table('alumnos')->where('id', $id)->delete();
        return redirect('/admin/alumnos')->with('exito', 'Alumno eliminado correctamente.');
    }

    // --- ASIGNACIONES Y HORARIOS ---
    public function indexHorarios()
    {
        $this->verificarAdmin();
        $docentes = DB::table('docentes')->orderBy('apellidos')->get();
        $cursos = DB::table('cursos')->orderBy('nombre')->get();
        
        $asignaciones = DB::table('docente_curso')
            ->join('docentes', 'docente_curso.docente_id', '=', 'docentes.id')
            ->join('cursos', 'docente_curso.curso_id', '=', 'cursos.id')
            ->select('docente_curso.id as docente_curso_id', 'docentes.dni', 'docentes.nombres', 'docentes.apellidos', 'cursos.codigo', 'cursos.nombre')
            ->get();

        return view('admin.horarios', compact('docentes', 'cursos', 'asignaciones'));
    }

    public function guardarAsignacion(Request $request)
    {
        $this->verificarAdmin();
        $datos = $request->validate([
            'docente_id' => 'required|integer',
            'curso_id' => 'required|integer',
            'seccion' => 'required|string|max:10',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);

        DB::table('docente_curso')->updateOrInsert(
            ['docente_id' => $datos['docente_id'], 'curso_id' => $datos['curso_id']],
            ['updated_at' => now(), 'created_at' => now()]
        );

        $docenteCursoId = DB::table('docente_curso')
            ->where('docente_id', $datos['docente_id'])
            ->where('curso_id', $datos['curso_id'])
            ->value('id');

        DB::table('horarios')->insert([
            'docente_curso_id' => $docenteCursoId,
            'seccion' => strtoupper($datos['seccion']),
            'dia_semana' => $datos['dia_semana'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_fin' => $datos['hora_fin'],
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/horarios')->with('exito', 'Horario y asignación guardados correctamente.');
    }
}
