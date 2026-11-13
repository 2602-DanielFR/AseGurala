<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Throwable;

class SesionClaseController extends Controller
{
    private function filtrarColumnas(string $tabla, array $datos): array
    {
        $cols = array_map('strtolower', Schema::getColumnListing($tabla));
        return array_filter(
            $datos,
            fn ($k) => in_array(strtolower($k), $cols, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function obtenerEjecutablePython(): string
    {
        $rutaLaragon = 'C:\\laragon\\bin\\python\\python-3.13\\python.exe';
        if (file_exists($rutaLaragon)) {
            return $rutaLaragon;
        }
        return env('PYTHON_PATH', 'python');
    }

    /**
     * Lista oficial de los 35 alumnos de prueba enviados para el curso.
     * El campo 'codigo' va del '1' al '35' (para coincidir si en el ZKTeco MB10-VL tienen ID 1, 2, 3...)
     * y también tienen un DNI asignado. Además se cruza por nombre si el biométrico envía el nombre.
     */
    private function obtenerPadronAlumnosPrueba(): array
    {
        return [
            ['codigo' => '1',  'dni' => '70000001', 'apellidos' => 'ALVA BERROSPI',         'nombres' => 'ANGELA XIMENA'],
            ['codigo' => '2',  'dni' => '70000002', 'apellidos' => 'ALVARADO CAMPOS',       'nombres' => 'BRENDA'],
            ['codigo' => '3',  'dni' => '70000003', 'apellidos' => 'BARRAGAN CERDAN',       'nombres' => 'CESAR ALBERTO'],
            ['codigo' => '4',  'dni' => '70000004', 'apellidos' => 'BARRUETA MARCELLINI',   'nombres' => 'CESAR RAFAEL'],
            ['codigo' => '5',  'dni' => '70000005', 'apellidos' => 'CAJAS BERNARDO',        'nombres' => 'JHAN CRHISTIAN'],
            ['codigo' => '6',  'dni' => '70000006', 'apellidos' => 'CEFERINO OLORTIN',      'nombres' => 'LEONARDO JAREN'],
            ['codigo' => '7',  'dni' => '70000007', 'apellidos' => 'CUARITE ESTRADA',       'nombres' => 'CARLOS MANUEL'],
            ['codigo' => '8',  'dni' => '70000008', 'apellidos' => 'CUEVA SANTAMARIA',      'nombres' => 'LEITO OSCAR'],
            ['codigo' => '9',  'dni' => '70000009', 'apellidos' => 'DUEÑAS LOYOLA',         'nombres' => 'YHOZIRA MILAGROS'],
            ['codigo' => '10', 'dni' => '70000010', 'apellidos' => 'ESPINOZA CASTAÑEDA',    'nombres' => 'ADAMARIS VALENTINA'],
            ['codigo' => '11', 'dni' => '70000011', 'apellidos' => 'ESPINOZA DELGADO',      'nombres' => 'MARCO NICCOLO'],
            ['codigo' => '12', 'dni' => '70000012', 'apellidos' => 'ESTRADA RIVERA',        'nombres' => 'JAMIL'],
            ['codigo' => '13', 'dni' => '70000013', 'apellidos' => 'FERNANDEZ RAMOS',       'nombres' => 'JUAN JOSE'],
            ['codigo' => '14', 'dni' => '70000014', 'apellidos' => 'FRANCISCO ROJAS',       'nombres' => 'DANIEL'],
            ['codigo' => '15', 'dni' => '70000015', 'apellidos' => 'GODOY CONDEZO',         'nombres' => 'ALEJANDRO JEANPIER'],
            ['codigo' => '16', 'dni' => '70000016', 'apellidos' => 'HUANCAYA RECINES',      'nombres' => 'ERICSSON JUNIOR'],
            ['codigo' => '17', 'dni' => '70000017', 'apellidos' => 'LOZANO LEANDRO',        'nombres' => 'TAYLOR GEOFRE'],
            ['codigo' => '18', 'dni' => '70000018', 'apellidos' => 'MANZANO SOTO',          'nombres' => 'FRANK DAYVIS'],
            ['codigo' => '19', 'dni' => '70000019', 'apellidos' => 'MENDOZA LOPEZ',         'nombres' => 'MARCO ANTONIO'],
            ['codigo' => '20', 'dni' => '70000020', 'apellidos' => 'MENDOZA VILLANUEVA',    'nombres' => 'MAYRA MIRIAM'],
            ['codigo' => '21', 'dni' => '70000021', 'apellidos' => 'NALVARTE MANZANO',      'nombres' => 'ORLANDO BRYAM'],
            ['codigo' => '22', 'dni' => '70000022', 'apellidos' => 'PEÑA RAMÍREZ',          'nombres' => 'KAROL ANDREA'],
            ['codigo' => '23', 'dni' => '70000023', 'apellidos' => 'POMA RECINAS',          'nombres' => 'LADY DAYANA'],
            ['codigo' => '24', 'dni' => '70000024', 'apellidos' => 'POMA SANCHEZ',          'nombres' => 'ROBERTO CARLOS'],
            ['codigo' => '25', 'dni' => '70000025', 'apellidos' => 'QUIROZ LUNA',           'nombres' => 'JULIO CESAR'],
            ['codigo' => '26', 'dni' => '70000026', 'apellidos' => 'RAFAEL RAMON',          'nombres' => 'HELI HIDETOSHI'],
            ['codigo' => '27', 'dni' => '70000027', 'apellidos' => 'RIOS SALDAÑA',          'nombres' => 'KLEYVIN ADIEL'],
            ['codigo' => '28', 'dni' => '70000028', 'apellidos' => 'ROJAS MALDONADO',       'nombres' => 'PERCY JOHAN'],
            ['codigo' => '29', 'dni' => '70000029', 'apellidos' => 'SALAZAR TARAZONA',      'nombres' => 'JORGE LUIS'],
            ['codigo' => '30', 'dni' => '70000030', 'apellidos' => 'SANCHEZ CELIS',         'nombres' => 'ALEXIS DEL PIERO'],
            ['codigo' => '31', 'dni' => '70000031', 'apellidos' => 'SANTIAGO JUAN DE DIOS', 'nombres' => 'YEZER'],
            ['codigo' => '32', 'dni' => '70000032', 'apellidos' => 'SARAVIA LEON',          'nombres' => 'WILLIAM ANTONIO'],
            ['codigo' => '33', 'dni' => '70000033', 'apellidos' => 'SIMON GONZALES',        'nombres' => 'RONALDO JHOSEP'],
            ['codigo' => '34', 'dni' => '70000034', 'apellidos' => 'SORIA BLAS',            'nombres' => 'LUSDITH DIANA'],
            ['codigo' => '35', 'dni' => '70000035', 'apellidos' => 'TORRES PALOMINO',       'nombres' => 'CARLOS ADOLFO ANTONIO'],
            ['codigo' => '36', 'dni' => '70000036', 'apellidos' => 'TRUJILLO AVILA',        'nombres' => 'MARCEL ALEXANDER JAVIER'],
            ['codigo' => '37', 'dni' => '70000037', 'apellidos' => 'VALDIZAN ARCE',         'nombres' => 'DIACK KENNEDY'],
            ['codigo' => '38', 'dni' => '70000038', 'apellidos' => 'VERASTEGUI SANTIAGO',   'nombres' => 'SILVANA DANITZA'],
            ['codigo' => '39', 'dni' => '70000039', 'apellidos' => 'VILCA ROSAS',           'nombres' => 'JOEL AUGUSTO'],
            ['codigo' => '40', 'dni' => '70000040', 'apellidos' => 'ZEGARRA PIMENTEL',      'nombres' => 'JHONNY CHRISTIAN'],
            ['codigo' => '41', 'dni' => '70000041', 'apellidos' => 'ZEVALLOS SALAS',        'nombres' => 'DIEGO ARNOLD'],
        ];
    }

    /**
     * Garantiza que las tablas de alumnos, curso_alumno y asistencias existan
     * y que la lista de 35 alumnos de prueba esté cargada.
     */
    private function asegurarTablasAsistencia(): void
    {
        if (!Schema::hasTable('alumnos')) {
            Schema::create('alumnos', function (Blueprint $table) {
                $table->id();
                $table->string('dni', 20)->unique();
                $table->string('codigo', 30)->nullable();
                $table->string('nombres', 120);
                $table->string('apellidos', 120)->default('');
                $table->string('email', 120)->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('curso_alumno')) {
            Schema::create('curso_alumno', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curso_id');
                $table->unsignedBigInteger('alumno_id');
                $table->string('seccion', 20)->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
                $table->unique(['curso_id', 'alumno_id']);
            });
        }

        if (!Schema::hasTable('asistencias')) {
            Schema::create('asistencias', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sesion_clase_id');
                $table->unsignedBigInteger('alumno_id');
                $table->string('estado', 20)->default('falta'); // asistio, falta
                $table->dateTime('hora_marcacion')->nullable();
                $table->string('origen', 30)->default('ZKTeco MB10-VL');
                $table->timestamps();
                $table->unique(['sesion_clase_id', 'alumno_id']);
            });
        }

        // Sembrar automáticamente el padrón de alumnos de la imagen si aún no están registrados
        $ahora = Carbon::now('America/Lima')->toDateTimeString();
        $padron = $this->obtenerPadronAlumnosPrueba();

        foreach ($padron as $item) {
            $existe = DB::table('alumnos')
                ->where('apellidos', $item['apellidos'])
                ->where('nombres', $item['nombres'])
                ->first();

            if (!$existe) {
                DB::table('alumnos')->insertOrIgnore($this->filtrarColumnas('alumnos', [
                    'dni' => $item['dni'],
                    'codigo' => $item['codigo'],
                    'nombres' => $item['nombres'],
                    'apellidos' => $item['apellidos'],
                    'estado' => true,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]));
            }
        }
    }

    private function enviarComandoArduino(string $comando): array
    {
        $respuestas = [
            'INICIAR_CLASE' => 'CLASE_INICIADA',
            'FINALIZAR_CLASE' => 'CLASE_FINALIZADA',
        ];

        try {
            $resultado = Process::timeout(8)->run([
                $this->obtenerEjecutablePython(),
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

    /**
     * Inicializa la lista de asistencia de la sesión con los alumnos del curso.
     */
    private function inicializarListaAlumnosSesion(int $sesionId, int $horarioId): void
    {
        $this->asegurarTablasAsistencia();

        $horario = DB::table('horarios')
            ->join('docente_curso', 'horarios.docente_curso_id', '=', 'docente_curso.id')
            ->where('horarios.id', $horarioId)
            ->select('horarios.*', 'docente_curso.curso_id')
            ->first();

        if (!$horario) {
            return;
        }

        $alumnosCurso = DB::table('curso_alumno')
            ->where('curso_id', $horario->curso_id)
            ->where('estado', true)
            ->pluck('alumno_id');

        if ($alumnosCurso->isEmpty()) {
            $alumnosCurso = DB::table('alumnos')
                ->where('estado', true)
                ->pluck('id');
        }

        $ahora = Carbon::now('America/Lima')->toDateTimeString();

        foreach ($alumnosCurso as $alumnoId) {
            DB::table('asistencias')->insertOrIgnore($this->filtrarColumnas('asistencias', [
                'sesion_clase_id' => $sesionId,
                'alumno_id' => $alumnoId,
                'estado' => 'falta',
                'hora_marcacion' => null,
                'origen' => 'ZKTeco MB10-VL',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]));
        }
    }

    /**
     * Devuelve el padrón de alumnos del curso incluso antes de iniciar la sesión,
     * para que se muestre la lista completa apenas se escanee el DNI del docente.
     */
    public function listarPadronCurso(Request $request)
    {
        $this->asegurarTablasAsistencia();

        $sesionId = $request->query('sesion_id');
        $horarioId = $request->query('horario_id');
        $cursoId = $request->query('curso_id');

        if ($sesionId) {
            $sesion = DB::table('sesiones_clase')->where('id', $sesionId)->first();
            if ($sesion) {
                $this->inicializarListaAlumnosSesion($sesion->id, $sesion->horario_id);
                $lista = $this->obtenerListaAsistenciaSesion($sesion->id);
                return response()->json([
                    'ok' => true,
                    'resumen' => $lista['resumen'],
                    'alumnos' => $lista['alumnos'],
                ]);
            }
        }

        // Si no se ha seleccionado ningún curso ni horario aún, no devolver nada
        if (!$horarioId && !$cursoId) {
            return response()->json([
                'ok' => true,
                'resumen' => [
                    'total' => 0,
                    'asistieron' => 0,
                    'faltaron' => 0,
                ],
                'alumnos' => [],
            ]);
        }

        if ($horarioId && !$cursoId) {
            $horario = DB::table('horarios')
                ->join('docente_curso', 'horarios.docente_curso_id', '=', 'docente_curso.id')
                ->where('horarios.id', $horarioId)
                ->select('docente_curso.curso_id')
                ->first();
            $cursoId = $horario->curso_id ?? null;
        }

        $query = DB::table('alumnos')
            ->where('alumnos.estado', true);

        if ($cursoId) {
            $tieneMatriculados = DB::table('curso_alumno')
                ->where('curso_id', $cursoId)
                ->where('estado', true)
                ->exists();

            if ($tieneMatriculados) {
                $query->join('curso_alumno', 'alumnos.id', '=', 'curso_alumno.alumno_id')
                      ->where('curso_alumno.curso_id', $cursoId)
                      ->where('curso_alumno.estado', true)
                      ->select('alumnos.*');
            }
        }

        $alumnos = $query
            ->orderBy('alumnos.apellidos')
            ->orderBy('alumnos.nombres')
            ->get()
            ->map(function ($alumno) {
                return [
                    'id' => $alumno->id,
                    'dni' => $alumno->dni,
                    'codigo' => $alumno->codigo,
                    'nombres' => $alumno->nombres,
                    'apellidos' => $alumno->apellidos,
                    'estado' => 'falta',
                    'hora_marcacion' => null,
                    'hora_formateada' => '-',
                    'origen' => 'ZKTeco MB10-VL',
                ];
            });

        $total = $alumnos->count();

        return response()->json([
            'ok' => true,
            'resumen' => [
                'total' => $total,
                'asistieron' => 0,
                'faltaron' => $total,
            ],
            'alumnos' => $alumnos,
        ]);
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

        $this->asegurarTablasAsistencia();

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
                    'mensaje' => 'Estás fuera del horario permitido (se habilita 10 min antes).',
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

            $arduino = $this->enviarComandoArduino(
                'INICIAR_CLASE'
            );

            // Si el Arduino no responde (ej. el biométrico ya está conectado directo a corriente para pruebas),
            // igual permitimos iniciar la sesión de clase para que el ZKTeco MB10-VL marque asistencia
            $respuestaArduino = $arduino['ok']
                ? $arduino['respuesta']
                : 'MODO_DIRECTO_SIN_ARDUINO';

            $id = DB::table('sesiones_clase')->insertGetId($this->filtrarColumnas('sesiones_clase', [
                'horario_id' => $horario->id,
                'fecha' => $fecha,
                'hora_inicio_real' => $ahora->toDateTimeString(),
                'hora_fin_real' => null,
                'estado' => 'iniciada',
                'created_at' => $ahora->toDateTimeString(),
                'updated_at' => $ahora->toDateTimeString(),
            ]));

            $this->inicializarListaAlumnosSesion($id, $horario->id);

            return response()->json([
                'ok' => true,
                'mensaje' => '¡Clase iniciada! Energizando biométrico ZKTeco MB10-VL...',
                'sesion_id' => $id,
                'hora_inicio' => $ahora->format('H:i:s'),
                'arduino' => $respuestaArduino ?? ($arduino['respuesta'] ?? 'OK'),
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

            try {
                $this->procesarSincronizacionBiometrico($sesion);
            } catch (Throwable $e) {
                report($e);
            }

            $arduino = $this->enviarComandoArduino(
                'FINALIZAR_CLASE'
            );
            $respuestaArduino = $arduino['ok']
                ? $arduino['respuesta']
                : 'MODO_DIRECTO_SIN_ARDUINO';

            DB::table('sesiones_clase')
                ->where('id', $sesion->id)
                ->update($this->filtrarColumnas('sesiones_clase', [
                    'hora_fin_real' => $ahora->toDateTimeString(),
                    'estado' => 'finalizada',
                    'updated_at' => $ahora->toDateTimeString(),
                ]));

            return response()->json([
                'ok' => true,
                'mensaje' => 'Clase finalizada y biométrico apagado correctamente.',
                'hora_fin' => $ahora->format('H:i:s'),
                'arduino' => $respuestaArduino ?? ($arduino['respuesta'] ?? 'OK'),
            ]);
        });
    }

    public function sincronizarBiometrico(Request $request, $sesion_id)
    {
        $this->asegurarTablasAsistencia();

        $sesion = DB::table('sesiones_clase')->where('id', $sesion_id)->first();

        if (!$sesion) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Sesión de clase no encontrada.'
            ], 404);
        }

        $resultadoSync = $this->procesarSincronizacionBiometrico($sesion);
        $lista = $this->obtenerListaAsistenciaSesion($sesion->id);

        return response()->json([
            'ok' => true,
            'biometrico_conectado' => $resultadoSync['conectado'],
            'mensaje_biometrico' => $resultadoSync['mensaje'],
            'nuevos_registrados' => $resultadoSync['nuevos'],
            'resumen' => $lista['resumen'],
            'alumnos' => $lista['alumnos'],
        ]);
    }

    public function listarAsistencia($sesion_id)
    {
        $this->asegurarTablasAsistencia();

        $sesion = DB::table('sesiones_clase')->where('id', $sesion_id)->first();
        if (!$sesion) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Sesión no encontrada.'
            ], 404);
        }

        $this->inicializarListaAlumnosSesion($sesion->id, $sesion->horario_id);
        $lista = $this->obtenerListaAsistenciaSesion($sesion->id);

        return response()->json([
            'ok' => true,
            'resumen' => $lista['resumen'],
            'alumnos' => $lista['alumnos'],
        ]);
    }

    private function procesarSincronizacionBiometrico(object $sesion): array
    {
        $this->inicializarListaAlumnosSesion($sesion->id, $sesion->horario_id);

        $horario = DB::table('horarios')
            ->join('docente_curso', 'horarios.docente_curso_id', '=', 'docente_curso.id')
            ->where('horarios.id', $sesion->horario_id)
            ->select('horarios.*', 'docente_curso.curso_id')
            ->first();

        $horaDesde = '00:00:00';
        if ($horario && $horario->hora_inicio) {
            $horaDesde = Carbon::parse($sesion->fecha . ' ' . $horario->hora_inicio, 'America/Lima')
                ->subMinutes(15)
                ->format('H:i:s');
        }

        try {
            $proceso = Process::timeout(12)->run([
                $this->obtenerEjecutablePython(),
                base_path('sincronizar_biometrico.py'),
                $sesion->fecha,
                $horaDesde,
            ]);

            $salida = json_decode(trim($proceso->output()), true);

            if (!is_array($salida) || empty($salida['conectado'])) {
                return [
                    'conectado' => false,
                    'mensaje' => $salida['mensaje'] ?? 'Encendiendo biométrico ZKTeco MB10-VL, esperando red Ethernet...',
                    'nuevos' => [],
                ];
            }

            $ahora = Carbon::now('America/Lima')->toDateTimeString();
            $nuevos = [];

            foreach (($salida['marcaciones'] ?? []) as $marca) {
                $identificador = trim((string) ($marca['user_id'] ?? ''));
                $nombreBio = strtoupper(trim((string) ($marca['nombre_biometrico'] ?? '')));

                if ($identificador === '' && $nombreBio === '') {
                    continue;
                }

                // 1. Buscar por DNI o por Código (1..41)
                $alumno = DB::table('alumnos')
                    ->where('dni', $identificador)
                    ->orWhere('codigo', $identificador)
                    ->first();

                // 2. Si en el biométrico no tiene espacios o tiene guiones (ej. ALVABERROSPI o ALVA-BERROSPI),
                // comparamos eliminando espacios, guiones y subguiones tanto del biométrico como de la BD
                if (!$alumno && $nombreBio !== '') {
                    $nombreSinEspacios = str_replace([' ', '-', '_', '.'], '', $nombreBio);
                    $alumno = DB::table('alumnos')
                        ->whereRaw("REPLACE(UPPER(CONCAT(apellidos, nombres)), ' ', '') LIKE ?", ["%{$nombreSinEspacios}%"])
                        ->orWhereRaw("REPLACE(UPPER(CONCAT(nombres, apellidos)), ' ', '') LIKE ?", ["%{$nombreSinEspacios}%"])
                        ->first();
                }

                if (!$alumno && $identificador !== '') {
                    $nombreCompleto = $nombreBio !== '' ? $nombreBio : ('ALUMNO ID ' . $identificador);
                    $partes = explode(' ', $nombreCompleto, 2);
                    $nombres = $partes[0];
                    $apellidos = $partes[1] ?? '';

                    $alumnoId = DB::table('alumnos')->insertGetId([
                        'dni' => $identificador,
                        'codigo' => $identificador,
                        'nombres' => $nombres,
                        'apellidos' => $apellidos,
                        'estado' => true,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);

                    if ($horario) {
                        DB::table('curso_alumno')->insertOrIgnore([
                            'curso_id' => $horario->curso_id,
                            'alumno_id' => $alumnoId,
                            'seccion' => $horario->seccion,
                            'estado' => true,
                            'created_at' => $ahora,
                            'updated_at' => $ahora,
                        ]);
                    }

                    $alumno = DB::table('alumnos')->where('id', $alumnoId)->first();
                }

                if ($alumno) {
                    $registroAsistencia = DB::table('asistencias')
                        ->where('sesion_clase_id', $sesion->id)
                        ->where('alumno_id', $alumno->id)
                        ->first();

                    if (!$registroAsistencia) {
                        DB::table('asistencias')->insert([
                            'sesion_clase_id' => $sesion->id,
                            'alumno_id' => $alumno->id,
                            'estado' => 'asistio',
                            'hora_marcacion' => $marca['timestamp'],
                            'origen' => 'ZKTeco MB10-VL',
                            'created_at' => $ahora,
                            'updated_at' => $ahora,
                        ]);
                        $nuevos[] = $alumno->dni;
                    } elseif ($registroAsistencia->estado !== 'asistio') {
                        DB::table('asistencias')
                            ->where('id', $registroAsistencia->id)
                            ->update([
                                'estado' => 'asistio',
                                'hora_marcacion' => $marca['timestamp'],
                                'origen' => 'ZKTeco MB10-VL',
                                'updated_at' => $ahora,
                            ]);
                        $nuevos[] = $alumno->dni;
                    }
                }
            }

            return [
                'conectado' => true,
                'mensaje' => 'Biométrico ZKTeco MB10-VL en línea. Escuchando marcaciones por Ethernet...',
                'nuevos' => array_values(array_unique($nuevos)),
            ];

        } catch (Throwable $e) {
            report($e);
            return [
                'conectado' => false,
                'mensaje' => 'Esperando respuesta del biométrico ZKTeco MB10-VL...',
                'nuevos' => [],
            ];
        }
    }

    private function obtenerListaAsistenciaSesion(int $sesionId): array
    {
        $alumnos = DB::table('asistencias')
            ->join('alumnos', 'asistencias.alumno_id', '=', 'alumnos.id')
            ->where('asistencias.sesion_clase_id', $sesionId)
            ->select(
                'alumnos.id',
                'alumnos.dni',
                'alumnos.codigo',
                'alumnos.nombres',
                'alumnos.apellidos',
                'asistencias.estado',
                'asistencias.hora_marcacion',
                'asistencias.origen'
            )
            ->orderBy('alumnos.apellidos')
            ->orderBy('alumnos.nombres')
            ->get()
            ->map(function ($item) {
                $item->hora_formateada = $item->hora_marcacion
                    ? Carbon::parse($item->hora_marcacion)->format('H:i:s')
                    : '-';
                return $item;
            });

        $total = $alumnos->count();
        $asistieron = $alumnos->where('estado', 'asistio')->count();
        $faltaron = $total - $asistieron;

        return [
            'resumen' => [
                'total' => $total,
                'asistieron' => $asistieron,
                'faltaron' => $faltaron,
            ],
            'alumnos' => $alumnos,
        ];
    }

    public function exportarExcel(Request $request)
    {
        $this->asegurarTablasAsistencia();

        $sesionId = $request->query('sesion_id');
        $horarioId = $request->query('horario_id');
        $cursoId = $request->query('curso_id');

        if ($sesionId) {
            $sesion = DB::table('sesiones_clase')->where('id', $sesionId)->first();
            if ($sesion) {
                $horarioId = $sesion->horario_id;
            }
        }

        $infoCurso = DB::table('horarios')
            ->join('docente_curso', 'horarios.docente_curso_id', '=', 'docente_curso.id')
            ->join('cursos', 'docente_curso.curso_id', '=', 'cursos.id')
            ->join('docentes', 'docente_curso.docente_id', '=', 'docentes.id')
            ->when($horarioId, fn($q) => $q->where('horarios.id', $horarioId))
            ->when(!$horarioId && $cursoId, fn($q) => $q->where('cursos.id', $cursoId))
            ->select(
                'cursos.id as curso_id',
                'cursos.codigo as curso_codigo',
                'cursos.nombre as curso_nombre',
                'horarios.id as horario_id',
                'horarios.seccion',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'docentes.dni as docente_dni',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos'
            )
            ->first();

        $sesionesQuery = DB::table('sesiones_clase')
            ->join('horarios', 'sesiones_clase.horario_id', '=', 'horarios.id')
            ->join('docente_curso', 'horarios.docente_curso_id', '=', 'docente_curso.id')
            ->when($sesionId, fn($q) => $q->where('sesiones_clase.id', $sesionId))
            ->when(!$sesionId && $horarioId, fn($q) => $q->where('sesiones_clase.horario_id', $horarioId))
            ->when(!$sesionId && !$horarioId && $cursoId, fn($q) => $q->where('docente_curso.curso_id', $cursoId))
            ->select(
                'sesiones_clase.id',
                'sesiones_clase.fecha',
                'sesiones_clase.hora_inicio_real',
                'sesiones_clase.hora_fin_real',
                'sesiones_clase.estado',
                'horarios.seccion'
            )
            ->orderBy('sesiones_clase.fecha', 'desc')
            ->orderBy('sesiones_clase.id', 'desc')
            ->get();

        $sesionesIds = $sesionesQuery->pluck('id')->toArray();

        if (!empty($sesionesIds)) {
            $registros = DB::table('asistencias')
                ->join('alumnos', 'asistencias.alumno_id', '=', 'alumnos.id')
                ->join('sesiones_clase', 'asistencias.sesion_clase_id', '=', 'sesiones_clase.id')
                ->whereIn('asistencias.sesion_clase_id', $sesionesIds)
                ->select(
                    'sesiones_clase.fecha',
                    'alumnos.dni',
                    'alumnos.codigo',
                    'alumnos.apellidos',
                    'alumnos.nombres',
                    'asistencias.estado',
                    'asistencias.hora_marcacion',
                    'asistencias.origen'
                )
                ->orderBy('sesiones_clase.fecha', 'desc')
                ->orderBy('alumnos.apellidos')
                ->orderBy('alumnos.nombres')
                ->get();
        } else {
            // Si aún no se inició sesión hoy, exporta el padrón oficial del curso con estado pendiente
            $fechaHoy = Carbon::now('America/Lima')->toDateString();
            $registros = DB::table('alumnos')
                ->where('estado', true)
                ->orderBy('apellidos')
                ->orderBy('nombres')
                ->get()
                ->map(function ($al) use ($fechaHoy) {
                    return (object) [
                        'fecha' => $fechaHoy,
                        'dni' => $al->dni,
                        'codigo' => $al->codigo,
                        'apellidos' => $al->apellidos,
                        'nombres' => $al->nombres,
                        'estado' => 'falta',
                        'hora_marcacion' => null,
                        'origen' => 'ZKTeco MB10-VL',
                    ];
                });
        }

        $codigoCurso = $infoCurso->curso_codigo ?? 'CURSO';
        $fechaDescarga = Carbon::now('America/Lima')->format('Y-m-d_His');
        $nombreArchivo = "Asistencia_{$codigoCurso}_{$fechaDescarga}.xls";

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $html = view('reportes.asistencia_excel', [
            'infoCurso' => $infoCurso,
            'registros' => $registros,
            'fechaGeneracion' => Carbon::now('America/Lima')->format('d/m/Y H:i:s'),
        ])->render();

        return response("\xEF\xBB\xBF" . $html, 200, $headers);
    }

    /**
     * Recibe la marcación instantánea en menos de 1 segundo desde escuchar_tiempo_real.py
     * o desde el propio ZKTeco MB10-VL en cuanto dice "Acceso correcto".
     */
    public function marcarInstantaneo(Request $request)
    {
        $this->asegurarTablasAsistencia();

        $identificador = trim((string) $request->input('user_id', ''));
        $nombreBio = strtoupper(trim((string) $request->input('nombre_biometrico', '')));
        $timestamp = $request->input('timestamp', Carbon::now('America/Lima')->toDateTimeString());

        if ($identificador === '' && $nombreBio === '') {
            return response()->json(['ok' => false, 'mensaje' => 'Datos vacíos'], 422);
        }

        // Buscar la sesión de clase que esté 'iniciada' en este momento
        $sesion = DB::table('sesiones_clase')
            ->where('estado', 'iniciada')
            ->orderBy('id', 'desc')
            ->first();

        if (!$sesion) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No hay ninguna sesión de clase iniciada en este momento.'
            ]);
        }

        $this->inicializarListaAlumnosSesion($sesion->id, $sesion->horario_id);

        // 1. Buscar alumno por ID del ZKTeco (1..41, 101..115, 201..215) o por DNI
        $alumno = DB::table('alumnos')
            ->where('dni', $identificador)
            ->orWhere('codigo', $identificador)
            ->first();

        // 2. Buscar ignorando espacios o guiones (por si en el biométrico pusieron ALVABERROSPI sin espacio)
        if (!$alumno && $nombreBio !== '') {
            $nombreSinEspacios = str_replace([' ', '-', '_', '.'], '', $nombreBio);
            $alumno = DB::table('alumnos')
                ->whereRaw("REPLACE(UPPER(CONCAT(apellidos, nombres)), ' ', '') LIKE ?", ["%{$nombreSinEspacios}%"])
                ->orWhereRaw("REPLACE(UPPER(CONCAT(nombres, apellidos)), ' ', '') LIKE ?", ["%{$nombreSinEspacios}%"])
                ->first();
        }

        if (!$alumno) {
            return response()->json([
                'ok' => false,
                'mensaje' => "Usuario ID {$identificador} no encontrado en el padrón."
            ]);
        }

        $ahora = Carbon::now('America/Lima')->toDateTimeString();

        DB::table('asistencias')->updateOrInsert(
            [
                'sesion_clase_id' => $sesion->id,
                'alumno_id' => $alumno->id,
            ],
            $this->filtrarColumnas('asistencias', [
                'estado' => 'asistio',
                'hora_marcacion' => $timestamp,
                'origen' => 'ZKTeco MB10-VL',
                'updated_at' => $ahora,
                'created_at' => $ahora,
            ])
        );

        return response()->json([
            'ok' => true,
            'mensaje' => "Asistencia marcada para {$alumno->apellidos}, {$alumno->nombres}",
            'alumno' => $alumno->apellidos . ', ' . $alumno->nombres,
            'hora' => $timestamp
        ]);
    }
}
