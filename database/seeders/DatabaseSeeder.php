<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    private function filtrarColumnasExistentes(string $tabla, array $datos): array
    {
        $columnasReales = Schema::getColumnListing($tabla);
        $mapaMinusculas = array_map('strtolower', $columnasReales);

        return array_filter(
            $datos,
            fn ($key) => in_array(strtolower($key), $mapaMinusculas, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function run(): void
    {
        $ahora = Carbon::now('America/Lima')->toDateTimeString();

        // Asegurar que las tablas nuevas o columnas faltantes existan
        $migracion = require database_path('migrations/0001_01_01_000003_create_asistencia_tables.php');
        $migracion->up();

        // 1. USUARIOS DEL PANEL ADMINISTRATIVO WEB (/login)
        User::updateOrCreate(
            ['email' => 'admin@asegurala.edu.pe'],
            [
                'name' => 'Administrador AseGurala',
                'password' => Hash::make('admin12345'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'docente@asegurala.edu.pe'],
            [
                'name' => 'Docente de Prueba',
                'password' => Hash::make('docente12345'),
            ]
        );

        // 2. DOCENTES DE PRUEBA (Para escanear o digitar DNI en /asistencia)
        $docentesData = [
            [
                'dni' => '12345678',
                'nombres' => 'CARLOS ALBERTO',
                'apellidos' => 'MENDOZA SALAZAR',
                'email' => 'cmendoza@udh.edu.pe',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'dni' => '87654321',
                'nombres' => 'MARIA ELENA',
                'apellidos' => 'TORRES VILLANUEVA',
                'email' => 'mtorres@udh.edu.pe',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'dni' => '44556677',
                'nombres' => 'JORGE LUIS',
                'apellidos' => 'PAREDES HUAMAN',
                'email' => 'jparedes@udh.edu.pe',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ];

        $docenteIds = [];
        foreach ($docentesData as $doc) {
            $datosLimpios = $this->filtrarColumnasExistentes('docentes', $doc);
            DB::table('docentes')->updateOrInsert(
                ['dni' => $doc['dni']],
                $datosLimpios
            );
            $docenteIds[$doc['dni']] = DB::table('docentes')->where('dni', $doc['dni'])->value('id');
        }

        // 3. CURSOS DE PRUEBA (Cada docente con su curso propio)
        $cursosData = [
            [
                'codigo' => 'ING-SIS-01',
                'nombre' => 'SISTEMAS DE TIEMPO REAL Y BIOMETRÍA',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'ING-SIS-02',
                'nombre' => 'ARQUITECTURA DE COMPUTADORAS Y ARDUINO',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'ING-SIS-03',
                'nombre' => 'INGENIERÍA DE SOFTWARE II',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ];

        $cursoIds = [];
        foreach ($cursosData as $cur) {
            $datosLimpios = $this->filtrarColumnasExistentes('cursos', $cur);
            DB::table('cursos')->updateOrInsert(
                ['codigo' => $cur['codigo']],
                $datosLimpios
            );
            $cursoIds[$cur['codigo']] = DB::table('cursos')->where('codigo', $cur['codigo'])->value('id');
        }

        // 4. ASIGNACIÓN DOCENTE_CURSO Y HORARIOS (Habilitados los 7 días de la semana para pruebas inmediatas)
        $asignaciones = [
            ['dni' => '12345678', 'codigo_curso' => 'ING-SIS-01', 'seccion' => 'A'],
            ['dni' => '87654321', 'codigo_curso' => 'ING-SIS-02', 'seccion' => 'A'],
            ['dni' => '44556677', 'codigo_curso' => 'ING-SIS-03', 'seccion' => 'B'],
        ];

        foreach ($asignaciones as $asig) {
            $docId = $docenteIds[$asig['dni']];
            $curId = $cursoIds[$asig['codigo_curso']];

            $datosDocenteCurso = $this->filtrarColumnasExistentes('docente_curso', [
                'docente_id' => $docId,
                'curso_id' => $curId,
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            DB::table('docente_curso')->updateOrInsert(
                ['docente_id' => $docId, 'curso_id' => $curId],
                $datosDocenteCurso
            );

            $docenteCursoId = DB::table('docente_curso')
                ->where('docente_id', $docId)
                ->where('curso_id', $curId)
                ->value('id');

            for ($dia = 1; $dia <= 7; $dia++) {
                $existeHorario = DB::table('horarios')
                    ->where('docente_curso_id', $docenteCursoId)
                    ->where('dia_semana', $dia)
                    ->where('seccion', $asig['seccion'])
                    ->first();

                if (!$existeHorario) {
                    $datosHorario = $this->filtrarColumnasExistentes('horarios', [
                        'docente_curso_id' => $docenteCursoId,
                        'seccion' => $asig['seccion'],
                        'dia_semana' => $dia,
                        'hora_inicio' => '00:00:00',
                        'hora_fin' => '23:59:00',
                        'estado' => true,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);
                    DB::table('horarios')->insert($datosHorario);
                }
            }
        }

        // Limpiamos la tabla curso_alumno para reasignar alumnos distintos a cada curso/docente
        DB::table('curso_alumno')->delete();

        // 5A. GRUPO 1: LOS 41 ALUMNOS DE TU IMAGEN -> Asignados al Docente 1 (DNI 12345678 - Curso ING-SIS-01)
        $alumnosDocente1 = [
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

        // 5B. GRUPO 2: 15 ALUMNOS DISTINTOS -> Asignados al Docente 2 (DNI 87654321 - Curso ING-SIS-02)
        $alumnosDocente2 = [
            ['codigo' => '101', 'dni' => '71000101', 'apellidos' => 'ACOSTA VARGAS',         'nombres' => 'LUIS FERNANDO'],
            ['codigo' => '102', 'dni' => '71000102', 'apellidos' => 'BENITES CHAVEZ',        'nombres' => 'FIORELLA MILAGROS'],
            ['codigo' => '103', 'dni' => '71000103', 'apellidos' => 'CABRERA SOLIS',         'nombres' => 'DIEGO ALEJANDRO'],
            ['codigo' => '104', 'dni' => '71000104', 'apellidos' => 'DAVILA MORALES',        'nombres' => 'CAMILA VALERIA'],
            ['codigo' => '105', 'dni' => '71000105', 'apellidos' => 'ESCALANTE ROJAS',       'nombres' => 'SEBASTIAN MATEO'],
            ['codigo' => '106', 'dni' => '71000106', 'apellidos' => 'FLORES GUZMAN',         'nombres' => 'ANDREA LUCIA'],
            ['codigo' => '107', 'dni' => '71000107', 'apellidos' => 'GAMARRA PINEDO',        'nombres' => 'RODRIGO ALONSO'],
            ['codigo' => '108', 'dni' => '71000108', 'apellidos' => 'HERRERA PALACIOS',      'nombres' => 'VALENTINA SOFIA'],
            ['codigo' => '109', 'dni' => '71000109', 'apellidos' => 'IBAÑEZ CARRASCO',       'nombres' => 'GIANFRANCO'],
            ['codigo' => '110', 'dni' => '71000110', 'apellidos' => 'JIMENEZ PORTAL',        'nombres' => 'DANIELA XIOMARA'],
            ['codigo' => '111', 'dni' => '71000111', 'apellidos' => 'LINARES MEDINA',        'nombres' => 'KEVIN BRAYAN'],
            ['codigo' => '112', 'dni' => '71000112', 'apellidos' => 'MIRANDA ORTIZ',         'nombres' => 'PAOLA ESTEFANIA'],
            ['codigo' => '113', 'dni' => '71000113', 'apellidos' => 'NAVARRO QUISPE',        'nombres' => 'CRISTIAN DAVID'],
            ['codigo' => '114', 'dni' => '71000114', 'apellidos' => 'ORDOÑEZ VELA',          'nombres' => 'NATALIA FERNANDA'],
            ['codigo' => '115', 'dni' => '71000115', 'apellidos' => 'PAREDES AGUILAR',       'nombres' => 'MIGUEL ANGEL'],
        ];

        // 5C. GRUPO 3: 15 ALUMNOS DISTINTOS -> Asignados al Docente 3 (DNI 44556677 - Curso ING-SIS-03)
        $alumnosDocente3 = [
            ['codigo' => '201', 'dni' => '72000201', 'apellidos' => 'AGUIRRE MONTES',        'nombres' => 'GABRIEL OMAR'],
            ['codigo' => '202', 'dni' => '72000202', 'apellidos' => 'BUSTAMANTE RUIZ',       'nombres' => 'ARIANA NICOLE'],
            ['codigo' => '203', 'dni' => '72000203', 'apellidos' => 'CASTILLO VENTURA',      'nombres' => 'RENZO FABIAN'],
            ['codigo' => '204', 'dni' => '72000204', 'apellidos' => 'DELGADO TAPIA',         'nombres' => 'LUCERO MARICIELO'],
            ['codigo' => '205', 'dni' => '72000205', 'apellidos' => 'ENRIQUEZ SALVADOR',     'nombres' => 'JOSE ANTONIO'],
            ['codigo' => '206', 'dni' => '72000206', 'apellidos' => 'FUENTES CORDOVA',       'nombres' => 'MELISSA KARINA'],
            ['codigo' => '207', 'dni' => '72000207', 'apellidos' => 'GUTIERREZ PRADO',       'nombres' => 'ALVARO JOAQUIN'],
            ['codigo' => '208', 'dni' => '72000208', 'apellidos' => 'HIDALGO PONCE',         'nombres' => 'CLAUDIA BEATRIZ'],
            ['codigo' => '209', 'dni' => '72000209', 'apellidos' => 'INGA CERVANTES',        'nombres' => 'ERICK ALEXANDER'],
            ['codigo' => '210', 'dni' => '72000210', 'apellidos' => 'JUAREZ BAZAN',          'nombres' => 'ROCIO DEL PILAR'],
            ['codigo' => '211', 'dni' => '72000211', 'apellidos' => 'LAZO CARDENAS',         'nombres' => 'FABRICIO LEONEL'],
            ['codigo' => '212', 'dni' => '72000212', 'apellidos' => 'MONTOYA SOTO',          'nombres' => 'KATHERINE YULIANA'],
            ['codigo' => '213', 'dni' => '72000213', 'apellidos' => 'NUÑEZ BECERRA',         'nombres' => 'VICTOR HUGO'],
            ['codigo' => '214', 'dni' => '72000214', 'apellidos' => 'OCHOA ZAVALA',          'nombres' => 'YESSENIA MARIBEL'],
            ['codigo' => '215', 'dni' => '72000215', 'apellidos' => 'PALOMINO REYES',        'nombres' => 'SERGIO ARTURO'],
        ];

        $gruposPorCurso = [
            'ING-SIS-01' => ['seccion' => 'A', 'alumnos' => $alumnosDocente1],
            'ING-SIS-02' => ['seccion' => 'A', 'alumnos' => $alumnosDocente2],
            'ING-SIS-03' => ['seccion' => 'B', 'alumnos' => $alumnosDocente3],
        ];

        foreach ($gruposPorCurso as $codigoCurso => $grupo) {
            $cId = $cursoIds[$codigoCurso];

            foreach ($grupo['alumnos'] as $item) {
                $datosAlumno = $this->filtrarColumnasExistentes('alumnos', [
                    'dni' => $item['dni'],
                    'codigo' => $item['codigo'],
                    'nombres' => $item['nombres'],
                    'apellidos' => $item['apellidos'],
                    'email' => strtolower($item['codigo']) . '@udh.edu.pe',
                    'estado' => true,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);

                DB::table('alumnos')->updateOrInsert(
                    ['dni' => $item['dni']],
                    $datosAlumno
                );

                $alumnoId = DB::table('alumnos')->where('dni', $item['dni'])->value('id');

                $datosCursoAlumno = $this->filtrarColumnasExistentes('curso_alumno', [
                    'curso_id' => $cId,
                    'alumno_id' => $alumnoId,
                    'seccion' => $grupo['seccion'],
                    'estado' => true,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);

                DB::table('curso_alumno')->updateOrInsert(
                    ['curso_id' => $cId, 'alumno_id' => $alumnoId],
                    $datosCursoAlumno
                );
            }
        }
    }
}
