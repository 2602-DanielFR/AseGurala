<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - AseGurala</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0b1220;
            color: #f8fafc;
            padding: 24px;
        }
        .contenedor { max-width: 1000px; margin: 0 auto; }
        .barra-superior {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #172033;
            border: 1px solid #263449;
            padding: 18px 24px;
            border-radius: 14px;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .acciones-top { display: flex; gap: 10px; align-items: center; }
        .btn {
            padding: 10px 16px;
            border-radius: 8px;
            border: 0;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background: #2563eb;
        }
        .btn-verde { background: #15803d; }
        .btn-rojo { background: #dc2626; }
        .tarjeta {
            background: #172033;
            border: 1px solid #263449;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
        }
        h2 { margin: 0 0 14px; font-size: 18px; color: #93c5fd; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #263449; }
        th { background: #0b1220; color: #94a3b8; font-size: 12px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="contenedor">
        <header class="barra-superior">
            <div>
                <h1 style="margin: 0; font-size: 20px;">PANEL DE CONTROL - ASEGURALA</h1>
                <div style="color: #94a3b8; font-size: 13px; margin-top: 4px;">
                    Conectado como: <strong>{{ $usuario->name }}</strong> ({{ $usuario->email }})
                </div>
            </div>
            <div class="acciones-top">
                <a href="{{ url('/admin/docentes') }}" class="btn" style="background: #0f766e;">⚙️ Gestionar CRUDs (Admin)</a>
                <a href="{{ url('/asistencia') }}" class="btn">▦ Ir al Escáner de DNI</a>
                <form method="POST" action="{{ url('/logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-rojo">Cerrar Sesión</button>
                </form>
            </div>
        </header>

        <section class="tarjeta">
            <h2>1. Docentes y Cursos Asignados (Con Alumnos Diferenciados)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Código Curso</th>
                        <th>Nombre del Curso</th>
                        <th>Alumnos Matriculados</th>
                        <th>Exportar Padrón / Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cursos as $c)
                        <tr>
                            <td><strong>{{ $c->codigo }}</strong></td>
                            <td>{{ $c->nombre }}</td>
                            <td>{{ $c->total_alumnos }} alumnos</td>
                            <td>
                                <a href="{{ url('/asistencia/exportar-excel?curso_id=' . $c->id) }}" class="btn btn-verde" style="padding: 6px 12px; font-size: 12px;">
                                    📊 Descargar Excel
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="tarjeta">
            <h2>2. DNI de Docentes para Escanear en /asistencia</h2>
            <table>
                <thead>
                    <tr>
                        <th>DNI para Escanear</th>
                        <th>Docente</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($docentes as $d)
                        <tr>
                            <td><strong style="color: #4ade80; font-size: 15px;">{{ $d->dni }}</strong></td>
                            <td>{{ $d->nombres }} {{ $d->apellidos }}</td>
                            <td>{{ $d->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="tarjeta">
            <h2>3. Historial de Sesiones de Clase e Informes Excel</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Curso</th>
                        <th>Docente</th>
                        <th>Estado</th>
                        <th>Reporte</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiones as $s)
                        <tr>
                            <td>#{{ $s->id }}</td>
                            <td>{{ $s->fecha }}</td>
                            <td>{{ $s->curso_codigo }} - {{ $s->curso_nombre }} ({{ $s->seccion }})</td>
                            <td>{{ $s->docente_nombres }} {{ $s->docente_apellidos }}</td>
                            <td>{{ strtoupper($s->estado) }}</td>
                            <td>
                                <a href="{{ url('/asistencia/exportar-excel?sesion_id=' . $s->id) }}" class="btn btn-verde" style="padding: 6px 12px; font-size: 12px;">
                                    📊 Excel de Sesión
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8;">
                                Aún no se han iniciado sesiones de clase hoy.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
