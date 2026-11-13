<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Docente;

class DocenteController extends Controller
{
    public function buscar($dni)
    {
        $docente = Docente::where('dni', $dni)
            ->where('estado', true)
            ->first();

        if (!$docente) {
            return response()->json([
                'encontrado' => false,
                'mensaje' => 'Docente no encontrado'
            ]);
        }

        $cursos = DB::table('docente_curso')
            ->join('cursos', 'docente_curso.curso_id', '=', 'cursos.id')
            ->where('docente_curso.docente_id', $docente->id)
            ->where('cursos.estado', true)
            ->select(
                'docente_curso.id as docente_curso_id',
                'cursos.id',
                'cursos.codigo',
                'cursos.nombre'
            )
            ->get();

        $ahora = Carbon::now('America/Lima');
        $dia = $ahora->dayOfWeekIso;

        // Obtenemos los horarios del día actual primero (y si no tiene hoy, mostramos los de otros días)
        $horarios = DB::table('horarios')
            ->join(
                'docente_curso',
                'horarios.docente_curso_id',
                '=',
                'docente_curso.id'
            )
            ->join('cursos', 'docente_curso.curso_id', '=', 'cursos.id')
            ->where('docente_curso.docente_id', $docente->id)
            ->where('horarios.estado', true)
            ->where('cursos.estado', true)
            ->select(
                'horarios.id',
                'cursos.id as curso_id',
                'cursos.codigo',
                'cursos.nombre',
                'horarios.seccion',
                'horarios.dia_semana',
                'horarios.hora_inicio',
                'horarios.hora_fin'
            )
            ->orderByRaw('CASE WHEN horarios.dia_semana = ? THEN 0 ELSE 1 END', [$dia])
            ->orderBy('horarios.hora_inicio')
            ->get()
            ->map(function ($horario) use ($ahora, $dia) {
                $fecha = $ahora->toDateString();

                $inicio = Carbon::parse(
                    $fecha . ' ' . $horario->hora_inicio,
                    'America/Lima'
                );

                $fin = Carbon::parse(
                    $fecha . ' ' . $horario->hora_fin,
                    'America/Lima'
                );

                $sesion = DB::table('sesiones_clase')
                    ->where('horario_id', $horario->id)
                    ->where('fecha', $fecha)
                    ->first();

                $horario->sesion_id = $sesion->id ?? null;
                $horario->sesion_estado = $sesion->estado ?? null;

                $apertura = $inicio->copy()->subMinutes(10);
                $esDiaCorrecto = ((int) $horario->dia_semana === (int) $dia);

                $horario->es_hoy = $esDiaCorrecto;
                $horario->puede_iniciar =
                    !$sesion
                    && $esDiaCorrecto
                    && $ahora->greaterThanOrEqualTo($apertura)
                    && $ahora->lessThan($fin);

                return $horario;
            });

        // Filtrar para mostrar en el selector prioritariamente los horarios del día de hoy (o uno por curso/sección)
        $horariosHoy = $horarios->where('es_hoy', true)->values();
        $horariosParaSelector = $horariosHoy->isNotEmpty() ? $horariosHoy : $horarios->unique(fn ($h) => $h->curso_id . '-' . $h->seccion)->values();

        return response()->json([
            'encontrado' => true,
            'docente' => $docente,
            'cursos' => $cursos,
            'horarios' => $horariosParaSelector
        ]);
    }
}
