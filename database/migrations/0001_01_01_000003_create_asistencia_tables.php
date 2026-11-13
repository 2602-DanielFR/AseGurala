<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('docentes')) {
            Schema::create('docentes', function (Blueprint $table) {
                $table->id();
                $table->string('dni', 15)->unique();
                $table->string('nombres', 120);
                $table->string('apellidos', 120);
                $table->string('email', 120)->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cursos')) {
            Schema::create('cursos', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 30)->unique();
                $table->string('nombre', 150);
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('docente_curso')) {
            Schema::create('docente_curso', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('docente_id');
                $table->unsignedBigInteger('curso_id');
                $table->boolean('estado')->default(true);
                $table->timestamps();
                $table->unique(['docente_id', 'curso_id']);
            });
        }

        if (!Schema::hasTable('horarios')) {
            Schema::create('horarios', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('docente_curso_id');
                $table->string('seccion', 20)->default('A');
                $table->tinyInteger('dia_semana');
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sesiones_clase')) {
            Schema::create('sesiones_clase', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('horario_id');
                $table->date('fecha');
                $table->dateTime('hora_inicio_real')->nullable();
                $table->dateTime('hora_fin_real')->nullable();
                $table->string('estado', 30)->default('iniciada');
                $table->timestamps();
            });
        }

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
        } else {
            Schema::table('alumnos', function (Blueprint $table) {
                if (!Schema::hasColumn('alumnos', 'codigo')) {
                    $table->string('codigo', 30)->nullable()->after('dni');
                }
                if (!Schema::hasColumn('alumnos', 'email')) {
                    $table->string('email', 120)->nullable()->after('apellidos');
                }
                if (!Schema::hasColumn('alumnos', 'estado')) {
                    $table->boolean('estado')->default(true);
                }
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
                $table->string('estado', 20)->default('falta');
                $table->dateTime('hora_marcacion')->nullable();
                $table->string('origen', 30)->default('ZKTeco MB10-VL');
                $table->timestamps();
                $table->unique(['sesion_clase_id', 'alumno_id']);
            });
        } else {
            Schema::table('asistencias', function (Blueprint $table) {
                if (!Schema::hasColumn('asistencias', 'hora_marcacion')) {
                    $table->dateTime('hora_marcacion')->nullable();
                }
                if (!Schema::hasColumn('asistencias', 'origen')) {
                    $table->string('origen', 30)->default('ZKTeco MB10-VL');
                }
            });
        }
    }

    public function down(): void
    {
        // No eliminar tablas existentes en producción/pruebas
    }
};
