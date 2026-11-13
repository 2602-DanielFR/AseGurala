<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .titulo {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
        }
        .meta-label {
            background-color: #e2e8f0;
            font-weight: bold;
            border: 1px solid #94a3b8;
            padding: 6px;
        }
        .meta-val {
            border: 1px solid #94a3b8;
            padding: 6px;
        }
        th {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: bold;
            border: 1px solid #1e40af;
            padding: 8px;
            text-align: center;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .asistio {
            background-color: #dcfce7;
            color: #166534;
            font-weight: bold;
            text-align: center;
        }
        .falta {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
            text-align: center;
        }
        .centro {
            text-align: center;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="7" class="titulo">REPORTE DE ASISTENCIA DE ALUMNOS - BIOMÉTRICO ZKTECO MB10-VL</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td class="meta-label">CURSO:</td>
            <td colspan="3" class="meta-val">
                {{ ($infoCurso->curso_codigo ?? '') . ' - ' . ($infoCurso->curso_nombre ?? 'General') }}
            </td>
            <td class="meta-label">SECCIÓN:</td>
            <td colspan="2" class="meta-val">{{ $infoCurso->seccion ?? 'Única' }}</td>
        </tr>
        <tr>
            <td class="meta-label">DOCENTE:</td>
            <td colspan="3" class="meta-val">
                {{ ($infoCurso->docente_nombres ?? '') . ' ' . ($infoCurso->docente_apellidos ?? '') }}
                @if(!empty($infoCurso->docente_dni)) (DNI: {{ $infoCurso->docente_dni }}) @endif
            </td>
            <td class="meta-label">EXPORTADO:</td>
            <td colspan="2" class="meta-val">{{ $fechaGeneracion }}</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <thead>
            <tr>
                <th>N°</th>
                <th>FECHA SESIÓN</th>
                <th>DNI / CÓDIGO</th>
                <th>APELLIDOS Y NOMBRES DEL ALUMNO</th>
                <th>ESTADO</th>
                <th>HORA DE MARCACIÓN</th>
                <th>DISPOSITIVO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $index => $reg)
                <tr>
                    <td class="centro">{{ $index + 1 }}</td>
                    <td class="centro">{{ $reg->fecha }}</td>
                    <td class="centro" style="mso-number-format:'\@';">{{ $reg->dni ?: $reg->codigo }}</td>
                    <td>{{ trim($reg->apellidos . ', ' . $reg->nombres, ', ') }}</td>
                    <td class="{{ $reg->estado === 'asistio' ? 'asistio' : 'falta' }}">
                        {{ $reg->estado === 'asistio' ? 'ASISTIÓ' : 'FALTA' }}
                    </td>
                    <td class="centro">{{ $reg->hora_marcacion ?? '-' }}</td>
                    <td class="centro">{{ $reg->origen ?? 'ZKTeco MB10-VL' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="centro">No se encontraron registros de asistencia para este curso/sesión.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
