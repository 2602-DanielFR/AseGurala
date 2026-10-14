<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Carbon;
use Throwable;

class SesionClaseController extends Controller
{
    private function enviarComandoArduino(string $comando): array
    {
        $respuestas = [
            'INICIAR_CLASE' => 'CLASE_INICIADA',
            'FINALIZAR_CLASE' => 'CLASE_FINALIZADA',
        ];

        try {
            $resultado = Process::timeout(8)->run([
                'C:\\laragon\\bin\\python\\python-3.13\\python.exe',
                base_path('arduino_comando.py'),
                $comando,
            ]);

            $respuesta = trim($resultado->output());

            if (
                $resultado->successful() &&
                $respuesta === $respuestas[$comando]
            ) {
                return [
                    'ok' => true,
                    'respuesta' => $respuesta,
                ];
            }

            return [
                'ok' => false,
                'mensaje' => 'El Arduino no confirmó el comando. '
                    . trim($resultado->errorOutput() ?: $respuesta),
            ];
        } catch (Throwable $e) {
            report($e);

            return [
                'ok' => false,
                'mensaje' => 'No se pudo comunicar con el Arduino. '
                    . 'Verifica que esté conectado al puerto COM8.',
            ];
        }
    }

    public function iniciar(Request $request)
    {
        $datos = $request->validate([
            'horario_id' => [
                'required',
                'integer',
                'exists:horarios,id',
            ],
        ]);

        $ahora = Carbon::now('America/Lima');
        $fecha = $ahora->toDateString();
        $dia = $ahora->dayOfWeekIso;

        return DB::transaction(function () use (
            $datos,
            $ahora,
            $fecha,
            $dia
        ) {
            $horario = DB::table('horarios')
                ->where('id', $datos['horario_id'])
                ->where('estado', true)
                ->where('dia_semana', $dia)
                ->lockForUpdate()
                ->first();

            if (!$horario) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'No tienes una clase programada para hoy.',
                ], 422);
            }

            $inicio = Carbon::parse(
                "$fecha {$horario->hora_inicio}",
                'America/Lima'
            );

            $fin = Carbon::parse(
                "$fecha {$horario->hora_fin}",
                'America/Lima'
            );

            $apertura = $inicio->copy()->subMinutes(10);

            if ($ahora->lt($apertura) || $ahora->gte($fin)) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'Estás fuera del horario permitido.',
                ], 422);
            }

            $existente = DB::table('sesiones_clase')
                ->where('horario_id', $horario->id)
                ->where('fecha', $fecha)
                ->lockForUpdate()
                ->first();

            if ($existente) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => $existente->estado === 'iniciada'
                        ? 'Esta clase ya está iniciada.'
                        : 'Esta clase ya fue finalizada hoy.',
                ], 409);
            }

            // Activar el Arduino antes de registrar la sesión.
            $arduino = $this->enviarComandoArduino(
                'INICIAR_CLASE'
            );

            if (!$arduino['ok']) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => $arduino['mensaje'],
                ], 503);
            }

            $id = DB::table('sesiones_clase')->insertGetId([
                'horario_id' => $horario->id,
                'fecha' => $fecha,
                'hora_inicio_real' => $ahora->toDateTimeString(),
                'hora_fin_real' => null,
                'estado' => 'iniciada',
                'created_at' => $ahora->toDateTimeString(),
                'updated_at' => $ahora->toDateTimeString(),
            ]);

            return response()->json([
                'ok' => true,
                'mensaje' => '¡Clase iniciada correctamente!',
                'sesion_id' => $id,
                'hora_inicio' => $ahora->format('H:i:s'),
                'arduino' => $arduino['respuesta'],
            ]);
        });
    }

    public function finalizar(Request $request)
    {
        $datos = $request->validate([
            'sesion_id' => [
                'required',
                'integer',
                'exists:sesiones_clase,id',
            ],
        ]);

        $ahora = Carbon::now('America/Lima');

        return DB::transaction(function () use ($datos, $ahora) {
            $sesion = DB::table('sesiones_clase')
                ->where('id', $datos['sesion_id'])
                ->lockForUpdate()
                ->first();

            if (!$sesion || $sesion->estado !== 'iniciada') {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'La clase no está iniciada o ya fue finalizada.',
                ], 409);
            }

            // Apagar el Arduino antes de finalizar la sesión.
            $arduino = $this->enviarComandoArduino(
                'FINALIZAR_CLASE'
            );

            if (!$arduino['ok']) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => $arduino['mensaje'],
                ], 503);
            }

            DB::table('sesiones_clase')
                ->where('id', $sesion->id)
                ->update([
                    'hora_fin_real' => $ahora->toDateTimeString(),
                    'estado' => 'finalizada',
                    'updated_at' => $ahora->toDateTimeString(),
                ]);

            return response()->json([
                'ok' => true,
                'mensaje' => 'Clase finalizada correctamente.',
                'hora_fin' => $ahora->format('H:i:s'),
                'arduino' => $arduino['respuesta'],
            ]);
        });
    }
}
