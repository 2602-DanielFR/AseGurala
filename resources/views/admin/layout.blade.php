<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') - Panel Admin AseGurala</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #0b1220; color: #f8fafc; padding: 20px; }
        .contenedor { max-width: 1050px; margin: 0 auto; }
        .menu { display: flex; gap: 10px; background: #172033; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; justify-content: space-between; border: 1px solid #263449; }
        .menu-links { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { padding: 8px 14px; border-radius: 8px; border: 0; font-weight: bold; font-size: 13px; cursor: pointer; text-decoration: none; color: white; background: #2563eb; }
        .btn:hover { background: #1d4ed8; }
        .btn-verde { background: #15803d; }
        .btn-rojo { background: #dc2626; }
        .btn-secundario { background: #334155; }
        .tarjeta { background: #172033; border: 1px solid #263449; border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        h1, h2 { margin-top: 0; color: #93c5fd; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 10px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #263449; }
        th { background: #0b1220; color: #94a3b8; font-size: 12px; text-transform: uppercase; }
        label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px; color: #cbd5e1; }
        input, select { width: 100%; padding: 10px; border: 1px solid #475569; background: #0b1220; color: white; border-radius: 8px; font-size: 14px; margin-bottom: 12px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .alerta-exito { background: #14532d; color: #4ade80; border: 1px solid #166534; padding: 10px 14px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; }
        .alerta-error { background: #3b181e; color: #fda4af; border: 1px solid #7f1d1d; padding: 10px 14px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="menu">
            <div class="menu-links">
                <a href="{{ url('/dashboard') }}" class="btn btn-secundario">← Volver al Dashboard</a>
                <a href="{{ url('/admin/docentes') }}" class="btn">Docentes</a>
                <a href="{{ url('/admin/cursos') }}" class="btn">Cursos</a>
                <a href="{{ url('/admin/alumnos') }}" class="btn">Alumnos</a>
                <a href="{{ url('/admin/horarios') }}" class="btn">Horarios y Carga</a>
            </div>
            <a href="{{ url('/asistencia') }}" class="btn btn-verde">▦ Ir al Terminal Biomértico</a>
        </div>

        @if(session('exito'))
            <div class="alerta-exito">{{ session('exito') }}</div>
        @endif
        @if($errors->any())
            <div class="alerta-error">{{ $errors->first() }}</div>
        @endif

        @yield('contenido')
    </div>
</body>
</html>
